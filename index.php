<!DOCTYPE html>
<html>
<head>
    <title>Compte à rebours</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css" rel="stylesheet">

    <script src="https://code.jquery.com/jquery-1.11.3.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>

    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f0f8ff;
            font-family: Arial, sans-serif;
            overflow: hidden;
        }
        #countdown-container {
            text-align: center;
            color: #007bff;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
            transition: transform 0.5s;
        }
        #countdown-container:hover {
            transform: scale(1.05);
        }
        #event-name {
            font-size: 4em;
            margin-bottom: 20px;
        }
        #countdown {
            font-size: 3em;
        }
    </style>

    <script type="text/javascript">
        $(document).ready(function() {
            var defaultDate = moment().add(1, 'days').set({hour: 18, minute: 0});
            
            $('#datetimepicker').datetimepicker({
                locale: 'fr',
                format: 'DD/MM/YYYY HH:mm',
                defaultDate: defaultDate
            });

            $('input[name="event"]').val("test");

            $('form').on('submit', function(e) {
                e.preventDefault();
                var datetimeStr = $('#datetimepicker').data("DateTimePicker").date();
                var eventName = $('input[name="event"]').val();
                if (datetimeStr && eventName) {
                    var targetDate = datetimeStr.toDate();
                    $('#form-container').hide();
                    $('#countdown-container').show();
                    $('#event-name').text(eventName);
                    startCountdown(targetDate);
                }
            });

            function startCountdown(targetDate) {
                function updateCountdown() {
                    var now = new Date().getTime();
                    var distance = targetDate - now;

                    if (distance < 0) {
                        clearInterval(countdownInterval);
                        $('#countdown').text("L'événement a déjà eu lieu !");
                        return;
                    }

                    var days = Math.floor(distance / (1000 * 60 * 60 * 24));
                    var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                    var seconds = Math.floor((distance % (1000 * 60)) / 1000);

                    $('#countdown').text(`${days}j ${hours}h ${minutes}m ${seconds}s`);
                }

                updateCountdown();
                var countdownInterval = setInterval(updateCountdown, 1000);
            }
        });
    </script>
</head>
<body>
    <div id="form-container" class="container" style="max-width: 600px;">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title">TP - Azure - Joris PARMENTIER - Améline BODELE - 5 SRC - ESGI</h3>
            </div>
            <div class="panel-body">
                <form>
                    <div class="form-group">
                        <label>Nom de l'événement :</label>
                        <input name="event" type="text" class="form-control" required/>
                    </div>

                    <div class="form-group">
                        <label>Date et heure de l'événement :</label>
                        <div class='input-group date' id='datetimepicker'>
                            <input type='text' class="form-control" />
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                        </div>
                    </div>

                    <input type="submit" value="Calculer les jours" class="btn btn-primary" />
                </form>
            </div>
        </div>
    </div>
    <div id="countdown-container" style="display: none;">
        <div id="event-name"></div>
        <div id="countdown"></div>
    </div>
</body>
</html>
