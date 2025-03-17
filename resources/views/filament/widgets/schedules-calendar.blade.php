<div>
    <div id="calendar"></div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/6.1.8/index.global.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log("FullCalendar está rodando...");
            var calendarEl = document.getElementById('calendar');

            if (!calendarEl) {
                console.error("Elemento do calendário não encontrado!");
                return;
            }

            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                events: @json($events),
                eventClick: function(info) {
                    window.open(info.event.url, "_blank");
                }
            });

            calendar.render();
            console.log("FullCalendar foi inicializado.");
        });
    </script>

    <style>
        #calendar {
            width: 100%;
            max-width: 1000px;
            margin: auto;
        }
    </style>
</div>
