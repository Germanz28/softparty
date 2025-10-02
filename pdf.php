<DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Generar factura PDF</title>

    <!-- jsPDF + html2canvas -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

    <style>
        :root {
            --bg: #f3f6fb;
            --card: #ffffff;
            --accent: #0f6fff;
            --muted: #64748b;
            --panel: #eef6ff;
        }

        body {
            font-family: Inter, Segoe UI, Arial;
            background: var(--bg);
            margin: 28px;
            color: #0f172a
        }

        .container {
            max-width: 900px;
            margin: 0 auto
        }

        .card {
            background: var(--card);
            padding: 18px;
            border-radius: 10px;
            box-shadow: 0 6px 22px rgba(15, 23, 42, 0.06)
        }

        h1 {
            color: var(--accent);
            margin: 0 0 12px 0;
            font-size: 20px
        }

        form.row {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 14px
        }

        .field {
            flex: 1;
            min-width: 150px
        }

        label {
            display: block;
            font-size: 13px;
            color: var(--muted);
            margin-bottom: 6px
        }

        input[type="text"],
        input[type="time"] {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #e6eef9;
            border-radius: 8px;
            background: #fbfdff;
            font-size: 14px;
            color: #0f172a;
        }

        .controls {
            display: flex;
            gap: 10px;
            align-items: center;
            margin-bottom: 10px
        }

        button.btn {
            padding: 10px 14px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            font-weight: 600
        }

        .btn.primary {
            background: var(--accent);
            color: #fff
        }

        .btn.ghost {
            background: #fff;
            border: 1px solid #dbe8fb;
            color: var(--accent)
        }

        .preview-wrap {
            display: flex;
            gap: 14px;
            align-items: flex-start
        }

        /* Hacer la factura visual más pequeña en pantalla */
        .invoice {
            width: 250px;
            background: linear-gradient(180deg, #fff, #fbfdff);
            border-radius: 8px;
            padding: 16px;
            border: 1px solid #edf6ff;
            box-shadow: 0 6px 18px rgba(12, 14, 30, 0.04);
            transform: scale(0.90);
            transform-origin: top left;
        }

        .invoice header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px
        }

        .logo {
            background: var(--panel);
            padding: 8px 12px;
            border-radius: 6px;
            color: var(--accent);
            font-weight: 700
        }

        .meta {
            font-size: 12px;
            color: var(--muted)
        }

        .section {
            background: #fff;
            border-radius: 6px;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #f1f7ff
        }

        .row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 6px;
            font-size: 14px
        }

        .row .k {
            color: var(--muted);
            font-size: 13px
        }

        .total {
            font-weight: 700;
            color: #0b3a66
        }

        .small {
            font-size: 12px;
            color: var(--muted)
        }

        .note {
            font-size: 12px;
            color: var(--muted);
            margin-top: 6px
        }

        @media (max-width:900px) {
            .preview-wrap {
                flex-direction: column
            }

            .invoice {
                width: 100%;
                transform: none
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="card">
            <h1>Generar factura PDF</h1>

            <form id="form" class="row" onsubmit="return false;">
                <div class="field">
                    <label for="nombre">Nombre del documento</label>
                    <input id="nombre" type="text" placeholder="Nombre o cliente" value="Cliente de prueba">
                </div>
                <div class="field">
                    <label for="contacto">Número de contacto</label>
                    <input id="contacto" type="text" placeholder="Teléfono" value="+56 9 1234 5678">
                </div>
                <div class="field">
                    <label for="placa">Placa</label>
                    <input id="placa" type="text" placeholder="ABC-123" value="ABC-123">
                </div>
                <div class="field">
                    <label for="tipo">Tipo de vehículo</label>
                    <input id="tipo" type="text" placeholder="Auto / Moto" value="Auto">
                </div>
                <div class="field">
                    <label for="hora">Hora de entrada</label>
                    <input id="hora" type="time" value="08:30">
                </div>
                <div class="field">
                    <label for="horaSalida">Hora de salida</label>
                    <input id="horaSalida" type="time" value="11:15">
                </div>
            </form>

            <div class="controls">
                <button id="btnPreview" class="btn ghost" type="button">Actualizar vista</button>
                <button id="btnPdf" class="btn primary" type="button">Descargar PDF bonito</button>
            </div>

            <div class="preview-wrap">
                <div class="invoice" id="invoice">
                    <header>
                        <div>
                            <div class="logo">SoftParty</div>
                            <div class="meta small">Factura de entrada</div>
                        </div>
                        <div style="text-align:right">
                            <div class="small">Fecha</div>
                            <div id="invDate" style="font-weight:700"></div>
                        </div>
                    </header>

                    <div class="section">
                        <div class="row">
                            <div class="k">Nombre</div>
                            <div id="pNombre">-</div>
                        </div>
                        <div class="row">
                            <div class="k">Contacto</div>
                            <div id="pContacto">-</div>
                        </div>
                    </div>

                    <div class="section">
                        <div class="row">
                            <div class="k">Placa</div>
                            <div id="pPlaca">-</div>
                        </div>
                        <div class="row">
                            <div class="k">Tipo</div>
                            <div id="pTipo">-</div>
                        </div>
                        <div class="row">
                            <div class="k">Hora entrada</div>
                            <div id="pHora">-</div>
                        </div>
                        <div class="row">
                            <div class="k">Hora salida</div>
                            <div id="pHoraSalida">-</div>
                        </div>
                    </div>

                    <div class="section">
                        <div class="row">
                            <div class="k">Observaciones</div>
                            <div class="small">Gracias por preferirnos</div>
                        </div>

                        <div class="row" style="border-top:1px solid #f1f7ff;padding-top:8px;margin-top:6px">
                            <div class="k">Total a pagar</div>
                            <div id="pTotal" class="total">$0</div>
                        </div>
                    </div>

                </div>

                <div style="flex:1; min-width:260px;">
                    <div style="background:#fff; padding:12px;border-radius:8px;border:1px solid #eef6ff">
                        <h3 style="margin:0 0 8px 0; color:#0f172a; font-size:16px">Vista rápida</h3>
                        <div id="quick" class="small" style="color:var(--muted)"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        (function() {
            const $ = id => document.getElementById(id);
            const fields = ['nombre', 'contacto', 'placa', 'tipo', 'hora', 'horaSalida'];

            function parseTimeToSeconds(t) {
                if (!t) return null;
                const parts = t.split(':');
                if (parts.length < 2) return null;
                return parseInt(parts[0], 10) * 3600 + parseInt(parts[1], 10) * 60;
            }

            function calculateTotal(entradaStr, salidaStr, tipoVehiculo) {
                const entrada = parseTimeToSeconds(entradaStr);
                const salida = parseTimeToSeconds(salidaStr);
                if (entrada === null || salida === null) return { hours: 0, total: 0 };

                // Si la salida es menor o igual a la entrada, asumimos que salió al día siguiente
                let diff = salida - entrada;
                if (diff <= 0) diff += 24 * 3600;

                const horas = Math.ceil(diff / 3600);

                let tarifa = 0;
                const tipo = (tipoVehiculo || '').toUpperCase();
                if (tipo.includes('CAR') || tipo === 'CARRO' || tipo.includes('AUTO')) tarifa = 3000;
                else if (tipo.includes('MOT') || tipo === 'MOTO') tarifa = 2000;
                else if (tipo.includes('CAM') || tipo === 'CAMION') tarifa = 4000;
                else tarifa = 0;

                return { hours: horas, total: horas * tarifa, tarifa };
            }

            function formatCurrency(n) {
                return n.toLocaleString('es-CL', { style: 'currency', currency: 'CLP', minimumFractionDigits: 0 });
            }

            const update = () => {
                $('pNombre').textContent = $('nombre').value || '-';
                $('pContacto').textContent = $('contacto').value || '-';
                $('pPlaca').textContent = $('placa').value || '-';
                $('pTipo').textContent = $('tipo').value || '-';
                $('pHora').textContent = $('hora').value || '-';
                $('pHoraSalida').textContent = $('horaSalida').value || '-';
                $('invDate').textContent = new Date().toLocaleString();

                const calc = calculateTotal($('hora').value, $('horaSalida').value, $('tipo').value);
                $('pTotal').textContent = calc.total > 0 ? formatCurrency(calc.total) : '$0';

                $('quick').innerHTML = `
            <strong>${$('nombre').value || '-'}</strong><br>
            ${$('contacto').value || '-'} • ${$('placa').value || '-'} • ${$('tipo').value || '-'}<br>
            Entrada: ${$('hora').value || '-'} • Salida: ${$('horaSalida').value || '-'}<br>
            Horas: ${calc.hours} • Total: ${calc.total > 0 ? formatCurrency(calc.total) : '$0'}
        `;
            };

            // Inicializa vista
            update();

            document.getElementById('btnPreview').addEventListener('click', update);

            document.getElementById('btnPdf').addEventListener('click', async function() {
                update();

                const {
                    jsPDF
                } = window.jspdf;
                const doc = new jsPDF({
                    unit: 'pt',
                    format: 'a4',
                    orientation: 'portrait'
                });

                const invoiceEl = document.getElementById('invoice');

                // Renderizar más pequeño en el PDF: usar un width menor
                await doc.html(invoiceEl, {
                    html2canvas: {
                        scale: 2,
                        useCORS: true,
                        backgroundColor: null
                    },
                    x: 40,
                    y: 40,
                    width: 360,
                    callback: function(doc) {
                        const nombre = (document.getElementById('placa').value || 'sinplaca').replace(/\s+/g, '_');
                        const file = `factura_${nombre}_${Date.now()}.pdf`;
                        doc.save(file);
                    }
                });
            });

            // Enter en campos actualiza preview
            fields.forEach(f => {
                const el = $(f);
                el.addEventListener('change', update);
                el.addEventListener('input', update);
            });
        })();
    </script>
</body>

</html>
