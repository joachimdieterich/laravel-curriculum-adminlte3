<template >
    <div class="row ">
        <div class="col-12">

            <div id="navigator-item-1"
                 class="box box-objective nav-item-box-image pointer col-xs-12 my-1"
                 style="min-width: 200px !important;">
                <div
                    class="nav-item-box-image-size"
                    style='background: url("http://127.0.0.1:8000/media/3175/thumb") center top / cover no-repeat;'>
                </div>
                <span class="bg-white text-center p-1 overflow-auto nav-item-box">
                    <h1 class="h6 events-heading pt-1 hyphens nav-item-text">
                   Bewegungsfelder
               </h1>
                    <p class="text-muted small">...</p>
                </span>
                <div
                    class="symbol"
                    style="position: absolute; padding: 6px; z-index: 1; width: 30px; height: 40px; background-color: rgb(5, 131, 201); top: 0px; font-size: 1.2em; left: 10px;">
                    <i class="fa fa-map-signs text-white pt-2"></i>
                </div>
            </div>

            <div id="navigator-item-1"
                 class="box box-objective nav-item-box-image pointer col-xs-12 my-1"
                 style="min-width: 200px !important;">
                <div
                    class="nav-item-box-image-size"
                    style='background: url("http://127.0.0.1:8000/media/3176/thumb") center top / cover no-repeat;'>
                </div>
                <span class="bg-white text-center p-1 overflow-auto nav-item-box">
                    <h1 class="h6 events-heading pt-1 hyphens nav-item-text">
                   Trainingspläne
               </h1>
                    <p class="text-muted small">...</p>
                </span>
                <div
                    class="symbol"
                    style="position: absolute; padding: 6px; z-index: 1; width: 30px; height: 40px; background-color: rgb(5, 131, 201); top: 0px; font-size: 1.2em; left: 10px;">
                    <i class="fa fa-map-signs text-white pt-2"></i>
                </div>
            </div>

            <div id="navigator-item-1"
                 class="box box-objective nav-item-box-image pointer col-xs-12 my-1"
                 style="min-width: 200px !important;">
                <div
                    class="nav-item-box-image-size"
                    style='background: url("http://127.0.0.1:8000/media/3180/thumb") center top / cover no-repeat;'>
                </div>
                <span class="bg-white text-center p-1 overflow-auto nav-item-box">
                    <h1 class="h6 events-heading pt-1 hyphens nav-item-text">
                   Tipps und Tricks
               </h1>
                    <p class="text-muted small">...</p>
                </span>
                <div
                    class="symbol"
                    style="position: absolute; padding: 6px; z-index: 1; width: 30px; height: 40px; background-color: rgb(5, 131, 201); top: 0px; font-size: 1.2em; left: 10px;">
                    <i class="fa fa-map-signs text-white pt-2"></i>
                </div>
            </div>

            <div id="navigator-item-1"
                 class="box box-objective nav-item-box-image pointer col-xs-12 my-1"
                 style="min-width: 200px !important;">
                <div
                    class="nav-item-box-image-size"
                    style='background: url("http://127.0.0.1:8000/media/3179/thumb") center top / cover no-repeat;'>
                </div>
                <span class="bg-white text-center p-1 overflow-auto nav-item-box">
                    <h1 class="h6 events-heading pt-1 hyphens nav-item-text">
                   Für Lehrkräfte
               </h1>
                    <p class="text-muted small">...</p>
                </span>
                <div
                    class="symbol"
                    style="position: absolute; padding: 6px; z-index: 1; width: 30px; height: 40px; background-color: rgb(5, 131, 201); top: 0px; font-size: 1.2em; left: 10px;">
                    <i class="fa fa-map-signs text-white pt-2"></i>
                </div>
            </div>
        </div>

        <div class="col-12 pt-2">
            <!-- Bewegungsfeld -->
            <div class="card">
                <div class="card-header">

                    <i class="fas fa-calendar-day mr-1"></i>
                    Bewegungsfeld
                </div>
                <div class="card-body py-2">
                    <img class="pull-right"
                         src="http://127.0.0.1:8000/media/3175/thumb"/>

                </div>
            </div>

            <!-- Trainingspläne -->
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-calendar-day mr-1"></i>
                    Trainingspläne
                </div>
                <div class="card-body py-2">
                    <img class="pull-right"
                         src="http://127.0.0.1:8000/media/3176/thumb"/>
                    Hier ist dein MoveItRLP-Trainingsplan. Ein Trainingsplan dokumentiert deine Fortschritte und zeigt dir neue Übungen, um noch fitter zu werden. In deinem Trainingsplan findest du
                    allgemeine Fitness-Challenges und Special-Skills. Um richtig fit zu werden, gibt es jeden Monat Challenges für dich. Du selbst
                    entscheidest, wie viel du trainierst und damit auch, wie viel du dich verbesserst. Klicke einfach auf den aktuellen Monat. Dort findest
                    du eine genaue Erklärung zu jedem Plan. Jetzt aber genug gelesen! Denk immer dran: MoveIt – Immer in Bewegung bleiben!

                    Dein MoveItRLP-Team!

                </div>
            </div>

            <!-- Tipps und Tricks -->
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-calendar-day mr-1"></i>
                    Tipps und Tricks
                </div>
                <div class="card-body py-2">
                    <img class="pull-right"
                         src="http://127.0.0.1:8000/media/3180/thumb"/>
                    Tipps und Tricks

                </div>
            </div>

            <!-- Tipps und Tricks -->
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-calendar-day mr-1"></i>
                   Bereich für Lehrkräfte
                </div>
                <div class="card-body py-2">
                    <img class="pull-right"
                         src="http://127.0.0.1:8000/media/3179/thumb"/>
                   Bereich für Lehrkräfte

                </div>
            </div>
        </div>

    </div>



</template>

<script>

    export default {
        props: {
            subscribable_type: '',
            subscribable_id: '',
              },
        data() {
            return {
                plans: [],
                subscriptions: {},
                search: '',
                errors: {}
            }
        },
        methods: {
            loaderEvent(){
                axios.get('/planSubscriptions?subscribable_type='+this.subscribable_type + '&subscribable_id='+this.subscribable_id)
                    .then(response => {
                        this.subscriptions = response.data.subscriptions;
                    })
                    .catch(e => {
                        this.errors = e.data.errors;
                    });
            },
        },

        mounted() {
            this.loaderEvent();
        },
        components: {

        },
    }
</script>
