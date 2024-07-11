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
    
    <script type="text/javascript">
        
        $(document).ready(function(e)
        {
            // Initialiser le datepicker (https://github.com/eternicode/bootstrap-datepicker)
            $('#datepicker').datepicker(
            {
                orientation: "top auto",
                autoclose: true,
                format: "dd/mm/yyyy",
                language: "fr"
            });
        });
        
    </script>
</head>
<body>
    <div class="container" style="margin-top: 50px; max-width: 600px;">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title">Compte à rebours</h3>
            </div>
            <div class="panel-body">
                <?php
                    // Si le bouton de soumission a été pressé
                    if(isset($_POST['submit']))
                    {
                        // Obtenir le mois, le jour et l'année à partir du champ de formulaire (jj/mm/aaaa)
                        $date = explode('/', $_POST['date']);
                        $jour = $date[0];
                        $mois = $date[1];
                        $annee = $date[2];
                        
                        // Obtenir les secondes futures (heure, minute, seconde, mois, jour, année);
                        $futur = mktime(00, 00, 00, $mois, $jour, $annee);
                        
                        // Obtenir les secondes actuelles depuis l'Unix Epoch (minuit le 1er janvier 1970)
                        $actuel = time();
                        
                        // Calculer la différence en secondes
                        $difference = $futur - $actuel;
                        
                        // Calculer le nombre de jours
                        $nbjours = ceil($difference / 86400);
                        
                        // Vérifier si l'événement a lieu aujourd'hui
                        if($nbjours > 0)
                        {
                            echo "<h1>Il reste $nbjours jours avant {$_POST['event']} !</h1>";
                        } elseif ($nbjours == 0)  {
                            echo "<h1>{$_POST['event']} a lieu aujourd'hui !</h1>";
                        } else {
                            echo "<h1>{$_POST['event']} a déjà eu lieu !</h1>";    
                        }
                    }
                ?>
                    
                <form method="post" action="">
                    <div class="form-group">
                        <label>Nom de l'événement :</label>
                        <input name="event" type="text" class="form-control" required/>
                    </div>
                    
                    <div class="form-group">
                        <label>Date de l'événement :</label>
                        <input id="datepicker" name="date" type="text" class="form-control" required/>
                    </div>
        
                    <input name="submit" type="submit" value="Calculer les jours" class="btn btn-primary" />
                </form>
            </div>
        </div>
    </div>
</body>
</html>
