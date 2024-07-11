<!DOCTYPE html>
<html>
<head>
    <title>Compte à rebours</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.4.1/css/bootstrap-datepicker.min.css" rel="stylesheet">
    
    <script src="http://code.jquery.com/jquery-1.11.3.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.4.1/js/bootstrap-datepicker.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: white;
            font-family: Arial, sans-serif;
        }
        #countdown {
            font-size: 3em;
        }
    </style>

    <script type="text/javascript">
        $(document).ready(function() {
            $('#datepicker').datepicker({
                orientation: "top auto",
                autoclose: true,
                format: "dd/mm/yyyy",
                language: "fr"
            });

            $('form').on('submit', function(e) {
                e.preventDefault();
                var dateStr = $('#datepicker').val();
                var eventName = $('input[name="event"]').val();
                if (dateStr && eventName) {
                    var dateParts = dateStr.split('/');
                    var targetDate = new Date(dateParts[2], dateParts[1] - 1, dateParts[0]);
                    $('#form-container').hide();
                    $('#countdown').show();
                    startCountdown(targetDate, eventName);
                }
            });

            function startCountdown(targetDate, eventName) {
                function updateCountdown() {
                    var now = new Date().getTime();
                    var distance = targetDate - now;

                    if (distance < 0) {
                        clearInterval(countdownInterval);
                        $('#countdown').text(`${eventName} a déjà eu lieu !`);
                        return;
                    }

                    var days = Math.floor(distance / (1000 * 60 * 60 * 24));
                    var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                    var seconds = Math.floor((distance % (1000 * 60)) / 1000);

                    $('#countdown').text(`Il reste ${days}j ${hours}h ${minutes}m ${seconds}s avant ${eventName}`);
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
                <h3 class="panel-title">Compte à rebours</h3>
            </div>
            <div class="panel-body">
                <form>
                    <div class="form-group">
                        <label>Nom de l'événement :</label>
                        <input name="event" type="text" class="form-control" required/>
                    </div>
                    
                    <div class="form-group">
                        <label>Date de l'événement :</label>
                        <input id="datepicker" name="date" type="text" class="form-control" required/>
                    </div>
        
                    <input type="submit" value="Calculer les jours" class="btn btn-primary" />
                </form>
            </div>
        </div>
    </div>
    <div id="countdown" style="display: none;"></div>
</body>
</html>
