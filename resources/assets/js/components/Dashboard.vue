<template>
    <main class="main">
        <div class="card mx-auto">
            <div class="card-body">
                <div class="card-chart-container">
                    <div class="card-header">
                        <h4>Ventas</h4>
                    </div>
                    <div class="card-content">
                        <div class="ct-chart">
                            <canvas id="chartVentas"></canvas>
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
            ingresoVar: null,
            ingresoChart: null,
            ingresosData: [],
            ingresoTotalData: [],
            ingresoMesData: [],

            ventaVar: null,
            ventaChart: null,
            ventasData: [],
            ventaTotalData: [],
            ventaMesData: [],
        }
    },
    methods: {
        getIngresos() {
            let me = this;
            var url = '/dashboard';
            axios.get(url).then(function (response) {
                var respuesta = response.data;
                me.ingresosData = respuesta.ingresos;
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
                me.ventasData = respuesta.ventas;
                //cargamos los datos del chart
                me.loadVentas();
            })
                .catch(function (error) {
                    console.log(error);
                });
        },

        loadVentas() {
            let me = this;
            me.ventasData.map(function (x) {
                me.ventaMesData.push(x.mes);
                me.ventaTotalData.push(x.total);
            });
            me.ventaVar = document.getElementById('chartVentas').getContext('2d');

            const mesesDelAnio = ['en', 'feb', 'mar', 'abr', 'may', 'jun', 'jul', 'ago', 'sept', 'oct', 'nov', 'dic'];

            // Valores de ingreso inicializados en 0 para todos los meses
            const valoresVenta = Array(12).fill(0);

            // Asigna los valores de ingreso conocidos a sus respectivos meses
            me.ventaMesData.forEach((mes, index) => {
                const posicionMes = mes - 1; // Ajusta el índice del mes
                valoresVenta[posicionMes] = parseFloat(me.ventaTotalData[index]); // Asigna el valor del ingreso
            });

            const ctx = document.getElementById("chartVentas").getContext("2d");
            const gradient = ctx.createLinearGradient(600, 0, 600, 600);;
            gradient.addColorStop(0, 'rgba(0, 123,255, 0.5)');
            gradient.addColorStop(0.35, 'rgba(0, 123,255, 0.25)');
            gradient.addColorStop(1, 'rgba(0, 123,255, 0)');

            me.ventaChart = new Chart(me.ventaVar, {
                type: 'bar',
                data: {
                    labels: mesesDelAnio,
                    datasets: [{
                        label: 'Ventas',
                        data: valoresVenta,
                        backgroundColor: gradient,
                        borderColor: '#007bff',
                        borderWidth: 2,
                        pointBackgroundColor: '#007bff',
                        pointRadius: 4,
                        pointHoverRadius: 8,
                        fill: true,
                        lineTension: 0.4
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

    .card-chart-container {
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

    .card-chart-container {
        height: 80%;
        /* Utiliza el 80% de la altura vertical disponible */
        width: 100%;
        /* Mantiene el tamaño horizontal */
    }
}
</style>