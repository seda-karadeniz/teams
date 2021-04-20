<?php

$errors = [];
$teams = [];

define('MISSING_TEAM','Vous avez oublie de specifie une ou des equipes');
define('MISSING_FILE','le fichier text est absent');
define('NO_TEAM_YET','il n ya tjr pas dequipe à lister');
define('FILE_PATH','teams.txt');

if(!is_file(FILE_PATH)){
    $errors[] = MISSING_FILE; /*le crochet, ajouter un nvl element dans le tableau en js - push*/
}
else{
    $teams = file(FILE_PATH, FILE_IGNORE_NEW_LINES);
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        if($_POST['action'] === 'add'){
            $tn = $_POST['team-name'] ?? '';
            if(is_string($tn)){
                $teamName = trim($tn);/* trim permet denlever les caractere despace qui sont autour du contenu*/
                if($teamName){
                    $teams[] = $teamName;
                }
            }
        }

        //delete

        /*foreach ($teams as $k => $team){ // k = key
            $teams[$k] = $teams.PHP_EOL; //le . = concaténation, PHP_eol = end of ligne (retour a la ligne)
        }*/

        if($_POST['action'] === 'delete'){
            $tns = $_POST['team-name']?? [];/*si quelque chose sappel bien teamname sinon stock un array vide*/
            if(is_array($tns)){

                $teams = array_diff($teams, $tns); /*faire teams moin teamsnames*/
            }
        }
        file_put_contents(FILE_PATH, array_map(fn($team) => $team.PHP_EOL, $teams));
    }

}
//sanitize filter
$teams = array_map(fn($team)=>filter_var($team, FILTER_SANITIZE_FULL_SPECIAL_CHARS), $teams);
?>
<!-- TEMPLATE D’AFFICHAGE -->

<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
    <title>Mes équipes</title>
</head>
<body>
<main class="container">
    <h1>Mes équipes</h1>
    <?php if ($errors): ?>
        <div class="alert alert-warning">
            <?php foreach ($errors as $error): ?>
                <ul class="list-group">
                    <li class="list-group-item"><?= $error ?></li>
                </ul>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <?php if ($teams): ?>
            <ul class="list-group">
                <?php foreach ($teams as $team): ?>
                    <li class="list-group-item"><?= $team ?></li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <div class="alert alert-warning">
                <p><?= NO_TEAM_YET ?></p>
            </div>
        <?php endif; ?>
        <section class="mt-5">
            <h2>Ajout d’une équipe</h2>
            <form action="<?= $_SERVER['PHP_SELF'] ?>" method="post">
                <label class="form-label" for="team-name">Nom de l’équipe</label>
                <input class="form-control"
                       type="text"
                       name="team-name"
                       id="team-name"
                       autofocus>
                <br>
                <button class="btn btn-primary form-control-sm mt-3"
                        type="submit">Ajouter l’équipe
                </button>
                <input type="hidden"
                       name="action"
                       value="add">
            </form>
        </section>
        <?php if ($teams): ?>
            <section class="mt-5">
                <h2>Suppression d’une ou de plusieurs équipes</h2>
                <form action="<?= $_SERVER['PHP_SELF'] ?>" method="post">
                    <ul class="list-group">
                        <?php foreach ($teams as $team): ?>
                            <li class="form-check list-group-item">
                                <input class="form-check-input"
                                       type="checkbox"
                                       id="<?= $team ?>"
                                       name="team-name[]"
                                       value="<?= $team ?>">
                                <label class="form-check-label"
                                       for="<?= $team ?>"><?= $team ?></label>
                            </li>
                        <?php endforeach; ?>
                    </ul>

                    <button class="btn btn-primary form-control-sm mt-3"
                            type="submit">Supprimer la ou les équipes sélectionné(es)
                    </button>
                    <input type="hidden"
                           name="action"
                           value="delete">
                </form>
            </section>
        <?php endif; ?>
    <?php endif; ?>
</main>
</body>
</html>
