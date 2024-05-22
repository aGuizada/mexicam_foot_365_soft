<template>
    <main class="main">
        <div class="container-fluid vw-100 vh-100 p-0">
            <div class="card-body">
                <div class="card-chart">
                    <div class="card-header">
                        <h4>Ventas</h4>
                    </div>
                    <div class="card-content">
                        <div class="ct-chart">
                            <canvas id="ventas"></canvas>
                        </div>
                    </div>
                    <div class="card-footer">
                        <p>Ventas de los últimos meses.</p>
                    </div>
                </div>
            </div>
        </div>
    </main>
</template>
<script>
export default {
    data() {
        return {
            varIngreso: null,
            charIngreso: null,
            ingresos: [],
            varTotalIngreso: [],
            varMesIngreso: [],

            varVenta: null,
            charVenta: null,
            ventas: [],
            varTotalVenta: [],
            varMesVenta: [],
        }
    },
    methods: {
        getIngresos() {
            let me = this;
            var url = '/dashboard';
            axios.get(url).then(function (response) {
                var respuesta = response.data;
                me.ingresos = respuesta.ingresos;
                //cargamos los datos del chart
                me.loadIngresos();
            })
                .catch(function (error) {
                    console.log(error);
                });
        },
        getVentas() {
            let me = this;
            var url = '/dashboard';
            axios.get(url).then(function (response) {
                var respuesta = response.data;
                me.ventas = respuesta.ventas;
                //cargamos los datos del chart
                me.loadVentas();
            })
                .catch(function (error) {
                    console.log(error);
                });
        },

        loadVentas() {
            let me = this;
            me.ventas.map(function (x) {
                me.varMesVenta.push(x.mes);
                me.varTotalVenta.push(x.total);
            });
            me.varVenta = document.getElementById('ventas').getContext('2d');

            const mesesDelAnio = ['en', 'feb', 'mar', 'abr', 'may', 'jun', 'jul', 'ago', 'sept', 'oct', 'nov', 'dic'];

            // Valores de ingreso inicializados en 0 para todos los meses
            const valoresVenta = Array(12).fill(0);

            // Asigna los valores de ingreso conocidos a sus respectivos meses
            me.varMesVenta.forEach((mes, index) => {
                const posicionMes = mes - 1; // Ajusta el índice del mes
                valoresVenta[posicionMes] = parseFloat(me.varTotalVenta[index]); // Asigna el valor del ingreso
            });

            const ctx = document.getElementById("ventas").getContext("2d");
            const gradient = ctx.createLinearGradient(600, 0, 600, 600);;
            gradient.addColorStop(0, 'rgba(0, 123,255, 0.5)');
            gradient.addColorStop(0.35, 'rgba(0, 123,255, 0.25)');
            gradient.addColorStop(1, 'rgba(0, 123,255, 0)');

            me.charVenta = new Chart(me.varVenta, {
                type: 'bar',
                data: {
                    //labels: me.varMesIngreso,
                    labels: mesesDelAnio,
                    datasets: [{
                        label: 'Ventas',
                        data: valoresVenta,
                        /*backgroundColor: '#ffc107',
                        fill: false,
                        borderColor: 'rgba(255, 99, 132, 0.2)',
                        borderWidth: 1,
                        tension: 0.1
                        */
                        backgroundColor: gradient, // Color de fondo para el área sombreada
                        borderColor: '#007bff',
                        borderWidth: 2, // Grosor de la línea
                        pointBackgroundColor: '#007bff', // Color del punto en la línea
                        pointRadius: 4, // Tamaño del punto en la línea
                        pointHoverRadius: 8, // Tamaño del punto al pasar el ratón
                        fill: true, // Rellenar el área bajo la línea

                        lineTension: 0.4 // Curvatura de la línea
                    }]
                },
            });
        },
    },
    mounted() {
        this.getIngresos();
        this.getVentas();
    }
}
</script>
<style>
/* Estilos para dispositivos móviles */
@media (max-width: 991.98px) {
    .card {
        width: 100%;

    }

    .card-chart {
        height: 100vh;
        /* Utiliza el 100% de la altura vertical disponible */
        width: 100%;
        /* Mantiene el tamaño horizontal */
    }

    .card-body {
        padding: 0px;
        height: 600px;
    }
}

/* Estilos para dispositivos de escritorio */
@media (min-width: 992px) {
    .card {
        width: 70%;
        margin: auto;
        /* Centra horizontalmente el card */
    }

    .card-chart {
        height: 80%;
        /* Utiliza el 80% de la altura vertical disponible */
        width: 100%;
        /* Mantiene el tamaño horizontal */
    }
}
</style>