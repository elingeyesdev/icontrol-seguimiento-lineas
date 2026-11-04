import os
from pathlib import Path

from docx import Document
from docx.enum.section import WD_SECTION
from docx.enum.table import WD_ALIGN_VERTICAL, WD_TABLE_ALIGNMENT
from docx.enum.text import WD_ALIGN_PARAGRAPH, WD_BREAK
from docx.oxml import OxmlElement
from docx.oxml.ns import qn
from docx.shared import Cm, Inches, Pt, RGBColor

ROOT = Path(r"C:\laragon\www\linea61v4")
SRC = ROOT / "PRUEBAS_CAJA_NEGRA_EJECUTADAS_MODULOS_TRANSACCIONALES.docx"
OUT = ROOT / "PRUEBAS_METODOS_TRANSACCIONALES.docx"

AZUL = RGBColor(0x0B, 0x3C, 0x78)
AZUL_MEDIO = RGBColor(0x1F, 0x5F, 0xA8)
GRIS = RGBColor(0x5A, 0x65, 0x73)
TEXTO = RGBColor(0x2B, 0x2B, 0x2B)
BLANCO = RGBColor(0xFF, 0xFF, 0xFF)

doc = Document(SRC)

# Vaciar el cuerpo conservando la configuración de página, estilos y encabezado.
body = doc.element.body
for child in list(body):
    if child.tag == qn("w:sectPr"):
        continue
    body.remove(child)

# Actualizar encabezado y pie conservando el campo PAGE.
for paragraph in doc.sections[0].header.paragraphs:
    for run in paragraph.runs:
        if "Línea 61" in run.text:
            run.text = "Línea 61  ·  Pruebas de métodos transaccionales"

for paragraph in doc.sections[0].footer.paragraphs:
    for run in paragraph.runs:
        if "Reporte de ejecución" in run.text:
            run.text = "Pruebas de métodos transaccionales · 29/10/2026   |   Página "


def set_cell_background(cell, color):
    tc_pr = cell._tc.get_or_add_tcPr()
    shd = tc_pr.find(qn("w:shd"))
    if shd is None:
        shd = OxmlElement("w:shd")
        tc_pr.append(shd)
    shd.set(qn("w:val"), "clear")
    shd.set(qn("w:color"), "auto")
    shd.set(qn("w:fill"), color)


def set_cell_margins(cell, top=50, start=80, bottom=50, end=80):
    tc = cell._tc
    tc_pr = tc.get_or_add_tcPr()
    tc_mar = tc_pr.first_child_found_in("w:tcMar")
    if tc_mar is None:
        tc_mar = OxmlElement("w:tcMar")
        tc_pr.append(tc_mar)
    for margin, value in (("top", top), ("start", start), ("bottom", bottom), ("end", end)):
        node = tc_mar.find(qn(f"w:{margin}"))
        if node is None:
            node = OxmlElement(f"w:{margin}")
            tc_mar.append(node)
        node.set(qn("w:w"), str(value))
        node.set(qn("w:type"), "dxa")


def set_table_borders(table, color="C9D3E0", size=4):
    tbl_pr = table._tbl.tblPr
    borders = tbl_pr.first_child_found_in("w:tblBorders")
    if borders is None:
        borders = OxmlElement("w:tblBorders")
        tbl_pr.append(borders)
    for edge in ("top", "left", "bottom", "right", "insideH", "insideV"):
        element = borders.find(qn(f"w:{edge}"))
        if element is None:
            element = OxmlElement(f"w:{edge}")
            borders.append(element)
        element.set(qn("w:val"), "single")
        element.set(qn("w:sz"), str(size))
        element.set(qn("w:space"), "0")
        element.set(qn("w:color"), color)


def set_repeat_table_header(row):
    tr_pr = row._tr.get_or_add_trPr()
    tbl_header = OxmlElement("w:tblHeader")
    tbl_header.set(qn("w:val"), "true")
    tr_pr.append(tbl_header)


def prevent_row_split(row):
    tr_pr = row._tr.get_or_add_trPr()
    cant_split = OxmlElement("w:cantSplit")
    cant_split.set(qn("w:val"), "true")
    tr_pr.append(cant_split)


def keep_table_together(table):
    """Evita que Word corte la tabla entre dos páginas."""
    rows = table.rows
    for index, row in enumerate(rows):
        prevent_row_split(row)
        if index == len(rows) - 1:
            continue
        for cell in row.cells:
            for paragraph in cell.paragraphs:
                paragraph.paragraph_format.keep_with_next = True


def set_col_width(cell, width):
    cell.width = width
    tc_pr = cell._tc.get_or_add_tcPr()
    tc_w = tc_pr.find(qn("w:tcW"))
    if tc_w is None:
        tc_w = OxmlElement("w:tcW")
        tc_pr.append(tc_w)
    tc_w.set(qn("w:w"), str(int(width.twips)))
    tc_w.set(qn("w:type"), "dxa")


def add_run(paragraph, text, size=10.5, bold=False, color=TEXTO, italic=False, font="Calibri"):
    run = paragraph.add_run(text)
    run.font.name = font
    run.font.size = Pt(size)
    run.font.bold = bold
    run.font.italic = italic
    run.font.color.rgb = color
    return run


def add_paragraph(text="", size=10.5, color=TEXTO, bold=False, align=None, before=0, after=4, italic=False, style=None):
    paragraph = doc.add_paragraph(style=style)
    if align is not None:
        paragraph.alignment = align
    paragraph.paragraph_format.space_before = Pt(before)
    paragraph.paragraph_format.space_after = Pt(after)
    if text:
        add_run(paragraph, text, size=size, bold=bold, color=color, italic=italic)
    return paragraph


def add_heading(text, level):
    paragraph = doc.add_heading(text, level=level)
    paragraph.paragraph_format.keep_with_next = True
    return paragraph


def add_caption(text):
    paragraph = add_paragraph(text, size=8.5, color=GRIS, after=4, italic=False)
    paragraph.paragraph_format.keep_with_next = True
    return paragraph


def add_table(headers, rows, widths, font_size=9, header_fill="0B3C78", align_center=True):
    table = doc.add_table(rows=1, cols=len(headers))
    table.alignment = WD_TABLE_ALIGNMENT.CENTER
    table.autofit = False
    set_table_borders(table)

    header_row = table.rows[0]
    set_repeat_table_header(header_row)
    prevent_row_split(header_row)
    for index, header in enumerate(headers):
        cell = header_row.cells[index]
        set_col_width(cell, widths[index])
        set_cell_background(cell, header_fill)
        set_cell_margins(cell)
        cell.vertical_alignment = WD_ALIGN_VERTICAL.CENTER
        paragraph = cell.paragraphs[0]
        paragraph.alignment = WD_ALIGN_PARAGRAPH.CENTER
        paragraph.paragraph_format.space_after = Pt(0)
        add_run(paragraph, header, size=font_size, bold=True, color=BLANCO)

    for row_index, values in enumerate(rows):
        row = table.add_row()
        prevent_row_split(row)
        cells = row.cells
        for index, value in enumerate(values):
            cell = cells[index]
            set_col_width(cell, widths[index])
            set_cell_margins(cell)
            cell.vertical_alignment = WD_ALIGN_VERTICAL.CENTER
            if row_index % 2 == 1:
                set_cell_background(cell, "F7F9FC")
            paragraph = cell.paragraphs[0]
            paragraph.paragraph_format.space_after = Pt(0)
            paragraph.paragraph_format.line_spacing = 1.0
            if align_center and index > 0 and len(str(value)) <= 14:
                paragraph.alignment = WD_ALIGN_PARAGRAPH.CENTER
            add_run(paragraph, str(value), size=font_size)

    keep_table_together(table)
    return table


def add_code_block(path, start, end, caption):
    add_caption(caption)
    lines = Path(path).read_text(encoding="utf-8").splitlines()
    code_lines = lines[start - 1:end]
    numbers = "\n".join(str(index) for index in range(start, end + 1))
    code = "\n".join(line.replace("\t", "    ") for line in code_lines)

    table = doc.add_table(rows=1, cols=2)
    table.alignment = WD_TABLE_ALIGNMENT.CENTER
    table.autofit = False
    set_table_borders(table, color="C9D3E0", size=4)
    prevent_row_split(table.rows[0])

    number_cell, code_cell = table.rows[0].cells
    set_col_width(number_cell, Inches(0.42))
    set_col_width(code_cell, Inches(6.45))
    set_cell_background(number_cell, "E9EEF5")
    set_cell_background(code_cell, "F7F9FC")
    set_cell_margins(number_cell, top=60, start=40, bottom=60, end=40)
    set_cell_margins(code_cell, top=60, start=80, bottom=60, end=80)

    p_numbers = number_cell.paragraphs[0]
    p_numbers.paragraph_format.space_after = Pt(0)
    p_numbers.paragraph_format.line_spacing = 1.0
    for line in numbers.splitlines():
        run = add_run(p_numbers, line, size=7.0, color=GRIS, font="Consolas")
        run.add_break()

    p_code = code_cell.paragraphs[0]
    p_code.paragraph_format.space_after = Pt(0)
    p_code.paragraph_format.line_spacing = 1.0
    for line in code.splitlines():
        run = add_run(p_code, line if line else " ", size=7.5, color=TEXTO, font="Consolas")
        run.add_break()

    add_paragraph("", after=2)


def add_bullet(title, text):
    paragraph = doc.add_paragraph(style="List Bullet")
    paragraph.paragraph_format.space_after = Pt(3)
    add_run(paragraph, title, bold=True, color=AZUL)
    add_run(paragraph, text)
    return paragraph


# ---------------------------------------------------------------------------
# Portada
# ---------------------------------------------------------------------------
add_paragraph("SISTEMA DE GESTIÓN DE TRANSPORTE  ·  LÍNEA 61", size=10, color=GRIS, bold=True, after=10)
add_paragraph("PRUEBAS DE MÉTODOS", size=30, color=AZUL, bold=True, after=0)
add_paragraph("TRANSACCIONALES", size=30, color=AZUL, bold=True, after=4)
add_paragraph("Un método por transacción", size=20, color=AZUL_MEDIO, after=2)
add_paragraph("Asignación de Turno  ·  Seguimiento GPS  ·  Control de Recorrido", size=11, after=2)
add_paragraph("Reporte de ejecución", size=14, color=GRIS, after=18)

metadata = [
    ("Documento", "Pruebas de métodos transaccionales – Un método por transacción"),
    ("Sistema", "Gestión de Transporte – Línea 61"),
    ("Versión", "1.0"),
    ("Fecha de ejecución", "29/10/2026"),
    ("Transacciones analizadas", "Asignación de Turno · Seguimiento GPS · Control de Recorrido"),
    ("Métodos analizados", "AsignacionTurnoService::crear · SeguimientoGpsService::registrarPunto · ControlRecorridoService::evaluarPunto"),
    ("Casos ejecutados", "15 casos · 5 por método"),
    ("Entorno", "Laravel 13.2 · PHP 8.4.12 · PHPUnit 12.5 · SQLite en memoria"),
    ("Resultado global", "15 pruebas · 45 aserciones · 0 fallos"),
    ("Elaborado por", "____________________________________________"),
]
table = doc.add_table(rows=0, cols=2)
table.alignment = WD_TABLE_ALIGNMENT.CENTER
table.autofit = False
set_table_borders(table)
for index, (label, value) in enumerate(metadata):
    cells = table.add_row().cells
    set_col_width(cells[0], Inches(1.55))
    set_col_width(cells[1], Inches(5.0))
    for cell in cells:
        set_cell_margins(cell, top=55, start=80, bottom=55, end=80)
        cell.vertical_alignment = WD_ALIGN_VERTICAL.CENTER
    if index % 2 == 1:
        set_cell_background(cells[0], "F7F9FC")
        set_cell_background(cells[1], "F7F9FC")
    p_label = cells[0].paragraphs[0]
    p_label.paragraph_format.space_after = Pt(0)
    add_run(p_label, label, size=9, bold=True, color=AZUL)
    p_value = cells[1].paragraphs[0]
    p_value.paragraph_format.space_after = Pt(0)
    add_run(p_value, value, size=9)

add_paragraph("", after=8)
paragraph = add_paragraph(
    "Documento de evidencia de ejecución. Cada transacción se documenta con un único método, "
    "el de mayor impacto en la regla de negocio. El código del método se presenta antes de la ficha, "
    "la captura del código de prueba y la tabla de casos ejecutados.",
    size=9,
    color=GRIS,
    align=WD_ALIGN_PARAGRAPH.CENTER,
    after=0,
)

# ---------------------------------------------------------------------------
# 1. Resumen ejecutivo
# ---------------------------------------------------------------------------
add_heading("1. Resumen ejecutivo", 1)
add_paragraph(
    "El presente informe concentra el análisis en un método por cada transacción del sistema Línea 61. "
    "Se seleccionaron los métodos que concentran las reglas de negocio y que son invocados por los "
    "controladores de la transacción: el alta de asignaciones de turno, el registro individual de "
    "posiciones GPS y la evaluación del recorrido contra las paradas de la ruta.",
)
add_paragraph(
    "Cada método se documenta con su código fuente, una ficha técnica, una captura del código de la "
    "prueba automatizada y un mínimo de cuatro casos de prueba. En esta ejecución se diseñaron y "
    "ejecutaron cinco casos por método, para un total de quince pruebas automatizadas.",
)
add_caption("Indicadores de la ejecución. Todos los casos planificados resultaron aprobados.")
add_table(
    ["Transacciones", "Métodos", "Casos", "Aserciones", "Fallidos"],
    [["3", "3", "15", "45", "0"]],
    [Inches(1.25)] * 5,
    font_size=13,
)

add_heading("1.1 Resultado por transacción", 2)
add_caption("Tabla 1. Resultado consolidado de los métodos analizados.")
add_table(
    ["Transacción", "Método analizado", "Casos", "Aprobados", "Fallidos", "Cumplimiento"],
    [
        ["Asignación de Turno", "AsignacionTurnoService::crear", "5", "5", "0", "100 %"],
        ["Seguimiento GPS", "SeguimientoGpsService::registrarPunto", "5", "5", "0", "100 %"],
        ["Control de Recorrido", "ControlRecorridoService::evaluarPunto", "5", "5", "0", "100 %"],
        ["Total", "3 métodos", "15", "15", "0", "100 %"],
    ],
    [Inches(1.25), Inches(2.15), Inches(0.55), Inches(0.7), Inches(0.55), Inches(1.0)],
    font_size=8.5,
)

add_heading("1.2 Lectura de los resultados", 2)
add_bullet(
    "Asignación de Turno. ",
    "El método crear impone el estado pendiente, descarta el estado y las horas enviadas por el cliente, "
    "y valida turno activo, recursos activos y disponibilidad antes de persistir.",
)
add_bullet(
    "Seguimiento GPS. ",
    "El método registrarPunto valida la ventana temporal y el estado de la asignación, delega la geocerca "
    "en ControlRecorridoService y garantiza que un mismo punto no genere un segundo registro.",
)
add_bullet(
    "Control de Recorrido. ",
    "El método evaluarPunto aplica la tolerancia de 80 m, marca las paradas intermedias como omitidas, "
    "avanza de Ida a Vuelta y cierra automáticamente la asignación al cumplir la última parada.",
)
add_bullet(
    "Cobertura. ",
    "Cada método cuenta con cinco casos: un camino válido, tres escenarios de rechazo o borde y un caso "
    "de integración con el estado persistido.",
)
add_bullet(
    "Trazabilidad. ",
    "Cada caso se ejecutó con una prueba automatizada y sus aserciones verifican el resultado devuelto y "
    "el estado en base de datos.",
)

# ---------------------------------------------------------------------------
# 2. Metodología
# ---------------------------------------------------------------------------
add_heading("2. Metodología y alcance", 1)
add_paragraph(
    "Las tres transacciones se componen de controladores, servicios y vistas. El análisis se enfoca en "
    "el método del servicio que implementa la regla de negocio central; los controladores exponen la "
    "operación y las vistas presentan el resultado, pero no contienen la lógica que se desea verificar.",
)
add_heading("2.1 Composición de las transacciones", 2)
add_caption("Tabla 2. Capas que participan en cada transacción.")
add_table(
    ["Transacción", "Controlador", "Servicio", "Vista"],
    [
        ["Asignación de Turno", "AsignacionTurnoApi · AsignacionTurnoController", "AsignacionTurnoService", "asignacion_turno.index / create / edit"],
        ["Seguimiento GPS", "SeguimientoGpsApiController", "SeguimientoGpsService", "monitoreo.index"],
        ["Control de Recorrido", "ControlRecorridoController", "ControlRecorridoService", "control_recorrido.index / show"],
    ],
    [Inches(1.35), Inches(2.0), Inches(1.6), Inches(1.9)],
    font_size=8.5,
)

add_heading("2.2 Criterio de selección del método", 2)
add_caption("Tabla 3. Método elegido por transacción y motivo de la selección.")
add_table(
    ["Transacción", "Método elegido", "Motivo de selección", "Complejidad ciclomática"],
    [
        ["Asignación de Turno", "AsignacionTurnoService::crear", "Concentra el alta y las validaciones de turno, recursos y disponibilidad.", "1"],
        ["Seguimiento GPS", "SeguimientoGpsService::registrarPunto", "Concentra la ingesta individual, la validación temporal, el estado y la deduplicación.", "2"],
        ["Control de Recorrido", "ControlRecorridoService::evaluarPunto", "Concentra la geocerca, la secuencia Ida/Vuelta, la omisión y el cierre automático.", "7"],
    ],
    [Inches(1.3), Inches(2.0), Inches(2.55), Inches(1.0)],
    font_size=8.5,
)
add_paragraph(
    "La complejidad ciclomática se calcula con la fórmula M = D + 1, donde D es el número de decisiones "
    "del método. Los valores permiten dimensionar la cantidad de caminos independientes y justificar los "
    "casos seleccionados.",
    size=9,
    color=GRIS,
)

add_heading("2.3 Estructura de cada sección", 2)
add_paragraph(
    "Cada transacción se presenta en el siguiente orden: (1) código fuente del método analizado con "
    "numeración de líneas; (2) tabla con la ficha del método; (3) captura del código de la prueba "
    "automatizada representativa; y (4) tabla de casos ejecutados con entrada, resultado esperado, "
    "resultado obtenido y estado.",
)

add_heading("2.4 Entorno de ejecución", 2)
add_caption("Tabla 4. Entorno y datos controlados.")
add_table(
    ["Elemento", "Configuración"],
    [
        ["Aplicación", "Sistema de Gestión de Transporte Línea 61"],
        ["Framework", "Laravel 13.2.0 · PHP 8.4.12 · PHPUnit 12.5.14"],
        ["Base de datos", "SQLite en memoria (phpunit.xml) con RefreshDatabase"],
        ["Archivo de pruebas", "tests/Feature/MetodosTransaccionalesTest.php"],
        ["Comando", "php artisan test --compact tests/Feature/MetodosTransaccionalesTest.php"],
        ["Datos base", "Turno activo/inactivo, ruta con dos paradas, conductores y micros activos"],
    ],
    [Inches(1.55), Inches(5.3)],
    font_size=9,
)

# ---------------------------------------------------------------------------
# 3. Asignación de Turno
# ---------------------------------------------------------------------------
add_heading("3. Transacción: Asignación de Turno", 1)
add_paragraph(
    "La transacción de Asignación de Turno registra la relación diaria entre conductor, micro, ruta y "
    "turno. El método analizado es el alta, porque concentra las reglas que impiden crear asignaciones "
    "duplicadas o con recursos fuera de servicio.",
)
add_heading("3.1 Código del método analizado", 2)
add_code_block(
    ROOT / "app/Services/AsignacionTurnoService.php",
    21,
    31,
    "Código 1. AsignacionTurnoService::crear() · app/Services/AsignacionTurnoService.php, líneas 21-31.",
)
add_heading("3.2 Ficha del método", 2)
add_caption("Tabla 5. Ficha técnica del método analizado.")
add_table(
    ["Atributo", "Detalle"],
    [
        ["Método", "AsignacionTurnoService::crear(array $datos): AsignacionTurno"],
        ["Archivo y líneas", "app/Services/AsignacionTurnoService.php · líneas 21-31"],
        ["Capa", "Servicio de negocio"],
        ["Propósito", "Crear una asignación válida en estado pendiente."],
        ["Entradas", "fecha, turno_id, ruta_id, micro_id, conductor_id y observaciones opcionales."],
        ["Salidas", "AsignacionTurno persistida en estado pendiente."],
        ["Dependencias", "validarTurnoActivo(), validarRecursosActivos(), validarDisponibilidad()"],
        ["Técnica aplicada", "Cobertura de decisiones y condiciones sobre las validaciones previas."],
        ["Complejidad ciclomática", "1 (sin decisiones propias; las validaciones se delegan)."],
    ],
    [Inches(1.55), Inches(5.3)],
    font_size=8.5,
)

add_heading("3.3 Captura del código de prueba", 2)
add_code_block(
    ROOT / "tests/Feature/MetodosTransaccionalesTest.php",
    112,
    129,
    "Captura 1. Prueba AT01 · alta válida con estado y horas enviados por el cliente.",
)

add_heading("3.4 Casos de prueba ejecutados", 2)
add_caption("Tabla 6. Casos del método AsignacionTurnoService::crear().")
add_table(
    ["ID", "Entrada", "Resultado esperado", "Resultado obtenido", "Estado"],
    [
        ["AT01", "Alta válida con estado=en_curso y horas informadas.", "Asignación en estado pendiente; horas nulas.", "estado=pendiente; hora_salida=null; hora_llegada=null.", "Aprobado"],
        ["AT02", "Alta con turno existente pero inactivo.", "ValidationException en turno_id; sin registro.", "Error en turno_id; 0 registros.", "Aprobado"],
        ["AT03", "Conductor ya asignado en la misma fecha y turno.", "ValidationException en conductor_id; conserva 1 registro.", "Error en conductor_id; 1 registro.", "Aprobado"],
        ["AT04", "Micro ya asignado en la misma fecha y turno.", "ValidationException en micro_id; conserva 1 registro.", "Error en micro_id; 1 registro.", "Aprobado"],
        ["AT05", "Micro existente con estado inactivo.", "ValidationException en micro_id; sin registro.", "Error en micro_id; 0 registros.", "Aprobado"],
    ],
    [Inches(0.5), Inches(2.0), Inches(1.85), Inches(1.85), Inches(0.65)],
    font_size=8,
)

# ---------------------------------------------------------------------------
# 4. Seguimiento GPS
# ---------------------------------------------------------------------------
add_heading("4. Transacción: Seguimiento GPS", 1)
add_paragraph(
    "La transacción de Seguimiento GPS recibe las posiciones del dispositivo móvil y las asocia al "
    "control de recorrido de la asignación. El método analizado es el registro individual, porque "
    "valida el punto, resuelve la geocerca y evita duplicados antes de persistir.",
)
add_heading("4.1 Código del método analizado", 2)
add_code_block(
    ROOT / "app/Services/SeguimientoGpsService.php",
    21,
    75,
    "Código 2. SeguimientoGpsService::registrarPunto() · app/Services/SeguimientoGpsService.php, líneas 21-75.",
)
add_heading("4.2 Ficha del método", 2)
add_caption("Tabla 7. Ficha técnica del método analizado.")
add_table(
    ["Atributo", "Detalle"],
    [
        ["Método", "SeguimientoGpsService::registrarPunto(AsignacionTurno $asignacion, array $datos): array"],
        ["Archivo y líneas", "app/Services/SeguimientoGpsService.php · líneas 21-75"],
        ["Capa", "Servicio de negocio"],
        ["Propósito", "Registrar un punto GPS y evaluar su efecto en el recorrido."],
        ["Entradas", "asignación y datos del punto: fecha_hora_gps, latitud, longitud y velocidad."],
        ["Salidas", "seguimiento_gps, control_recorrido y asignacion_estado."],
        ["Dependencias", "validarRangoTemporal(), ControlRecorridoService::evaluarPunto(), DB::transaction()"],
        ["Técnica aplicada", "Cobertura de decisiones y condiciones sobre estado, tiempo y deduplicación."],
        ["Complejidad ciclomática", "2 (una decisión de estado; el resto se delega)."],
    ],
    [Inches(1.55), Inches(5.3)],
    font_size=8.5,
)

add_heading("4.3 Captura del código de prueba", 2)
add_code_block(
    ROOT / "tests/Feature/MetodosTransaccionalesTest.php",
    208,
    226,
    "Captura 2. Prueba SG01 · registro de un punto dentro de la geocerca.",
)

add_heading("4.4 Casos de prueba ejecutados", 2)
add_caption("Tabla 8. Casos del método SeguimientoGpsService::registrarPunto().")
add_table(
    ["ID", "Entrada", "Resultado esperado", "Resultado obtenido", "Estado"],
    [
        ["SG01", "Asignación en_curso y punto sobre la primera parada.", "Control cumplido, distancia ≤80 m, asignación en_curso y 1 GPS.", "Control cumplido; distancia ≤80 m; en_curso; 1 GPS.", "Aprobado"],
        ["SG02", "Punto a más de 80 m de todas las paradas.", "Control general sin parada y observación «Seguimiento en ruta».", "ruta_parada_id=null; observación correcta; GPS asociado.", "Aprobado"],
        ["SG03", "Mismo punto enviado dos veces.", "Un único registro en seguimiento_gps.", "1 registro en seguimiento_gps.", "Aprobado"],
        ["SG04", "Punto sobre una asignación completada.", "ValidationException en asignacion_turno_id; sin registro.", "Error en asignacion_turno_id; 0 GPS.", "Aprobado"],
        ["SG05", "fecha_hora_gps tres minutos en el futuro.", "ValidationException en fecha_hora_gps; sin registro.", "Error en fecha_hora_gps; 0 GPS.", "Aprobado"],
    ],
    [Inches(0.5), Inches(2.0), Inches(1.85), Inches(1.85), Inches(0.65)],
    font_size=8,
)

# ---------------------------------------------------------------------------
# 5. Control de Recorrido
# ---------------------------------------------------------------------------
add_heading("5. Transacción: Control de Recorrido", 1)
add_paragraph(
    "La transacción de Control de Recorrido compara cada posición GPS con la secuencia planificada de "
    "paradas. El método analizado es la evaluación del punto, porque concentra la geocerca, el avance "
    "Ida/Vuelta, la omisión de paradas y el cierre automático de la asignación.",
)
add_heading("5.1 Código del método analizado", 2)
add_code_block(
    ROOT / "app/Services/ControlRecorridoService.php",
    56,
    113,
    "Código 3. ControlRecorridoService::evaluarPunto() · app/Services/ControlRecorridoService.php, líneas 56-113.",
)
add_heading("5.2 Ficha del método", 2)
add_caption("Tabla 9. Ficha técnica del método analizado.")
add_table(
    ["Atributo", "Detalle"],
    [
        ["Método", "ControlRecorridoService::evaluarPunto(AsignacionTurno $asignacion, SeguimientoGps $gps): ?ControlRecorrido"],
        ["Archivo y líneas", "app/Services/ControlRecorridoService.php · líneas 56-113"],
        ["Capa", "Servicio de negocio"],
        ["Propósito", "Determinar si un punto cumple la siguiente parada y actualizar el recorrido."],
        ["Entradas", "asignación con ruta y punto GPS con latitud, longitud y fecha/hora."],
        ["Salidas", "ControlRecorrido cumplido u omitido, o null si no coincide con una parada."],
        ["Dependencias", "paradasDeRuta(), controlesDeRuta(), secuenciaActiva(), calcularDistanciaMetros()"],
        ["Técnica aplicada", "Cobertura de decisiones, condiciones y caminos de la secuencia."],
        ["Complejidad ciclomática", "7 (seis decisiones: ruta, paradas, secuencia, parada nula, distancia y retorno)."],
    ],
    [Inches(1.55), Inches(5.3)],
    font_size=8.5,
)

add_heading("5.3 Captura del código de prueba", 2)
add_code_block(
    ROOT / "tests/Feature/MetodosTransaccionalesTest.php",
    391,
    406,
    "Captura 3. Prueba CR04 · cierre automático al cumplir la última parada.",
)

add_heading("5.4 Casos de prueba ejecutados", 2)
add_caption("Tabla 10. Casos del método ControlRecorridoService::evaluarPunto().")
add_table(
    ["ID", "Entrada", "Resultado esperado", "Resultado obtenido", "Estado"],
    [
        ["CR01", "Punto sobre la primera parada de una ruta con dos paradas.", "Control cumplido; asignación en_curso.", "Control cumplido; en_curso; distancia ≤80 m.", "Aprobado"],
        ["CR02", "Punto fuera de la geocerca con paradas planificadas.", "Retorna null; ninguna parada cumplida.", "null; 0 paradas cumplidas.", "Aprobado"],
        ["CR03", "Punto sobre la tercera parada de una secuencia O1-O2-O3.", "O3 cumplida; O1 y O2 omitidas.", "O3 cumplida; 2 omitidas.", "Aprobado"],
        ["CR04", "Punto sobre la última parada de la ruta.", "Asignación completada y hora_llegada registrada.", "estado=completado; hora_llegada no nula.", "Aprobado"],
        ["CR05", "Punto en I1 (Ida) y luego en V1 (Vuelta).", "Secuencia continúa en Vuelta y cierra la asignación.", "I1 cumplida; V1 cumplida [Vuelta]; completado.", "Aprobado"],
    ],
    [Inches(0.5), Inches(2.0), Inches(1.85), Inches(1.85), Inches(0.65)],
    font_size=8,
)

# ---------------------------------------------------------------------------
# 6. Resultados de ejecución
# ---------------------------------------------------------------------------
add_heading("6. Resultados de la ejecución", 1)
add_paragraph(
    "Las quince pruebas se ejecutaron contra los servicios de la aplicación con una base de datos SQLite "
    "en memoria y RefreshDatabase. Cada prueba crea sus propios datos y verifica el resultado devuelto y "
    "el estado persistido.",
)
add_caption("Comando ejecutado.")
code_table = doc.add_table(rows=1, cols=1)
code_table.alignment = WD_TABLE_ALIGNMENT.CENTER
set_table_borders(code_table, color="C9D3E0", size=4)
cell = code_table.rows[0].cells[0]
set_cell_background(cell, "F7F9FC")
set_cell_margins(cell, top=70, start=100, bottom=70, end=100)
p = cell.paragraphs[0]
p.paragraph_format.space_after = Pt(0)
add_run(p, "php artisan test --compact tests/Feature/MetodosTransaccionalesTest.php", size=8, font="Consolas", color=TEXTO)
add_paragraph("", after=6)

add_caption("Salida de PHPUnit.")
output_table = doc.add_table(rows=1, cols=1)
output_table.alignment = WD_TABLE_ALIGNMENT.CENTER
set_table_borders(output_table, color="C9D3E0", size=4)
cell = output_table.rows[0].cells[0]
set_cell_background(cell, "F7F9FC")
set_cell_margins(cell, top=70, start=100, bottom=70, end=100)
p = cell.paragraphs[0]
p.paragraph_format.space_after = Pt(0)
for line in ["  ...............", "", "  Tests:    15 passed (45 assertions)", "  Duration: 3.71s"]:
    run = add_run(p, line if line else " ", size=8, font="Consolas", color=TEXTO)
    run.add_break()

add_paragraph("", after=8)
add_caption("Tabla 11. Resultado por transacción.")
add_table(
    ["Transacción", "Pruebas", "Aserción", "Aprobadas", "Fallidas"],
    [
        ["Asignación de Turno", "5", "15", "5", "0"],
        ["Seguimiento GPS", "5", "15", "5", "0"],
        ["Control de Recorrido", "5", "15", "5", "0"],
        ["Total", "15", "45", "15", "0"],
    ],
    [Inches(2.0), Inches(0.9), Inches(0.9), Inches(1.0), Inches(0.9)],
    font_size=9,
)
add_paragraph(
    "La distribución de aserciones por transacción es uniforme: cada grupo de cinco pruebas verifica "
    "resultado devuelto, estado persistido y efecto en las entidades relacionadas.",
    size=9,
    color=GRIS,
)

# ---------------------------------------------------------------------------
# 7. Conclusiones
# ---------------------------------------------------------------------------
add_heading("7. Conclusiones", 1)
add_bullet(
    "Enfoque cumplido. ",
    "Se documentó un solo método por transacción, el de mayor impacto, con su código, ficha, captura de "
    "prueba y cinco casos ejecutados.",
)
add_bullet(
    "Resultado. ",
    "Las 15 pruebas finalizaron correctamente, con 45 aserciones y 0 fallos. Los tres métodos cumplen el "
    "comportamiento esperado en los escenarios analizados.",
)
add_bullet(
    "Regresión. ",
    "Las pruebas quedan incorporadas a la suite del proyecto y pueden repetirse con el comando indicado "
    "ante cambios en los servicios, controladores o reglas de negocio.",
)
add_bullet(
    "Siguiente paso. ",
    "Ampliar la cobertura al registro por lote, a la edición administrativa de asignaciones y a los "
    "escenarios de concurrencia sobre la desactivación de catálogos.",
)

# ---------------------------------------------------------------------------
# Anexo
# ---------------------------------------------------------------------------
add_heading("Anexo A. Archivos y endpoints de las transacciones", 1)
add_caption("Tabla 12. Archivos que componen cada transacción.")
add_table(
    ["Transacción", "Controlador", "Servicio", "Vista", "Prueba"],
    [
        ["Asignación de Turno", "app/Http/Controllers/AsignacionTurnoController.php · app/Http/Controllers/Api/AsignacionTurnoApi.php", "app/Services/AsignacionTurnoService.php", "resources/views/asignacion_turno/", "tests/Feature/MetodosTransaccionalesTest.php"],
        ["Seguimiento GPS", "app/Http/Controllers/Api/SeguimientoGpsApiController.php", "app/Services/SeguimientoGpsService.php", "resources/views/monitoreo/", "tests/Feature/MetodosTransaccionalesTest.php"],
        ["Control de Recorrido", "app/Http/Controllers/ControlRecorridoController.php", "app/Services/ControlRecorridoService.php", "resources/views/control_recorrido/", "tests/Feature/MetodosTransaccionalesTest.php"],
    ],
    [Inches(1.2), Inches(2.05), Inches(1.55), Inches(1.1), Inches(0.95)],
    font_size=7.5,
)
add_caption("Tabla 13. Endpoints que invocan los métodos analizados.")
add_table(
    ["Transacción", "Método HTTP y ruta", "Uso"],
    [
        ["Asignación de Turno", "POST /api/asignacion-turnos", "Alta de la asignación; invoca crear()."],
        ["Asignación de Turno", "POST /asignacion-turno", "Alta desde la interfaz web; invoca crear()."],
        ["Seguimiento GPS", "POST /api/mis/asignaciones/{id}/ubicaciones", "Registro individual; invoca registrarPunto()."],
        ["Control de Recorrido", "POST /api/mis/asignaciones/{id}/ubicaciones", "Evalúa la geocerca; invoca evaluarPunto()."],
        ["Control de Recorrido", "GET /control-recorrido/{id}", "Presenta el avance del recorrido en la vista."],
    ],
    [Inches(1.45), Inches(2.55), Inches(2.85)],
    font_size=8.5,
)

add_paragraph("", after=10)
add_paragraph(
    "Fin del reporte de métodos transaccionales · Línea 61 · 29/10/2026",
    size=9,
    color=GRIS,
    align=WD_ALIGN_PARAGRAPH.CENTER,
    after=0,
)

# Propiedades del documento.
doc.core_properties.title = "Pruebas de métodos transaccionales – Un método por transacción"
doc.core_properties.subject = "Sistema de Gestión de Transporte Línea 61"
doc.core_properties.author = "Equipo de pruebas Línea 61"
doc.core_properties.keywords = "métodos transaccionales, asignación de turno, seguimiento GPS, control de recorrido"
doc.core_properties.comments = "Un método por transacción con código, ficha, captura de prueba y cinco casos ejecutados."
doc.save(OUT)
print(f"Generado: {OUT}")
