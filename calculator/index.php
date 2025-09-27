<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Vela Aroma | Calculadora</title>    
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Vela Aroma">
    <meta name="description" content="Vela Aroma">
    <meta name="keywords" content="Vela Aroma">
    <link rel="icon" type="image/x-icon" href="./../images/favicon.ico">
    <link href="https://fonts.googleapis.com/css?family=Nunito&display=swap" rel="stylesheet"> 
    <link href="https://fonts.googleapis.com/css?family=Overpass&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="./../general/general.css">
</head>
<body>

<?php include_once "./../general/header.php"; ?>


    <h1>Calculadora de Costos de Velas</h1>
    <form id="formularioCostos">
        <label for="peso">Peso de la vela (gramos):</label>
        <input type="number" id="peso" name="peso" min="1" required>
        <button type="submit">Calcular</button>
    </form>
    <div id="resultados"></div>


<footer>
    <p>&copy; 2024, Vela Aroma. Todos los derechos reservados.</p>
</footer>
<?php include_once "./../js/js.php"; ?>
<?php include_once "./../general/cookies.php"; ?>

</body>
</html>
<script>
    document.getElementById('formularioCostos').addEventListener('submit', function(event) {
        event.preventDefault();

        // Obtener el peso ingresado (peso de la cera)
        const pesoCera = parseFloat(document.getElementById('peso').value);

        // Datos de materiales
        const materiales = [
            { material: "Cera de alto punto de fusión", unitario: 0.17, factor: 79 },
            { material: "Endurecedor", unitario: 0.18, factor: 11 },
            { material: "Fijador De Esencias Vegetal", unitario: 0.57, factor: 11 },
            { material: "Fragancia", unitario: 2.50, factor: 9 },
            { material: "Color", unitario: 0.06, factor: 7.9 }
        ];

        // Datos de costos fijos
        const costosFijos = [
            { material: "Costo de Luz", costo: 3.00 },
            { material: "Coste de Utensilios", costo: 5.14 },
            { material: "Caja/Envoltura", costo: 0.59 },
            { material: "Mechas de vela", costo: 0.33 },
            { material: "Pestañas de soporte", costo: 0.20 }
        ];

        // Calcular el peso total incluyendo aditivos
        const proporciones = 79 + 11 + 11; // Suma de factores de Cera, Endurecedor y Fijador
        const pesoTotal = pesoCera * (proporciones / 79); // Peso total incluyendo cera y aditivos

        // Calcular pesos proporcionales para Endurecedor y Fijador
        const pesoEndurecedor = (pesoTotal * 11) / proporciones;
        const pesoFijador = (pesoTotal * 11) / proporciones;

        // Calcular costos variables
        let total = 0;
        const resultadosVariables = materiales.map(mat => {
            let gramos;

            // Asignar pesos ajustados para los materiales específicos
            if (mat.material === "Cera de alto punto de fusión") {
                gramos = pesoCera;
            } else if (mat.material === "Endurecedor") {
                gramos = pesoEndurecedor;
            } else if (mat.material === "Fijador De Esencias Vegetal") {
                gramos = pesoFijador;
            } else {
                gramos = (pesoCera / 79) * mat.factor; // Cálculo para otros materiales
            }

            const costo = gramos * mat.unitario; // Calcular costo
            total += costo;

            return {
                material: mat.material,
                gramos: gramos.toFixed(2), // Redondear a 2 decimales
                costo: costo.toFixed(2)
            };
        });

        // Agregar costos fijos al total
        costosFijos.forEach(fijo => {
            total += fijo.costo;
        });

        // Calcular precio al público (doble del costo total + 7%)
        const precioPublico = total * 2 * 1.07;

        // Generar tabla de resultados
        let tabla = `
        <table>
            <tr>
                <th>Material</th>
                <th>Gramos Necesarios</th>
                <th>Costo</th>
            </tr>`;
        resultadosVariables.forEach(item => {
            tabla += `
            <tr>
                <td>${item.material}</td>
                <td>${item.gramos}</td>
                <td>$${item.costo}</td>
            </tr>`;
        });
        costosFijos.forEach(fijo => {
            tabla += `
            <tr>
                <td>${fijo.material}</td>
                <td>-</td>
                <td>$${fijo.costo.toFixed(2)}</td>
            </tr>`;
        });
        tabla += `
        <tr>
            <td colspan="2"><strong>Total</strong></td>
            <td><strong>$${total.toFixed(2)}</strong></td>
        </tr>
        <tr>
            <td colspan="2"><strong>Precio al Público</strong></td>
            <td><strong>$${precioPublico.toFixed(2)}</strong></td>
        </tr>
        </table>`;

        // Mostrar resultados
        document.getElementById('resultados').innerHTML = tabla;
    });
</script>



<style>

        table {
            border-collapse: collapse;
            width: 80%;
            margin: 20px auto;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }
        th {
            background-color: #f4f4f4;
        }
        h1 {
            text-align: center;
        }
        form {
            text-align: center;
            margin: 20px;
        }

</style>