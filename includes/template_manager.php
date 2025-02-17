<?php
require_once __DIR__ . '/phpquery_adapter.php';

class TemplateManager {
    private static $instance = null;
    private $pq;
    private $templatesCache = [];
    private $baseTemplatePath;

    private function __construct() {
        $this->pq = PhpQueryAdapter::getInstance();
        $this->baseTemplatePath = __DIR__ . '/../templates/';
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function loadTemplate($templatePath) {
        if (!isset($this->templatesCache[$templatePath])) {
            $fullPath = $this->baseTemplatePath . $templatePath;
            if (!file_exists($fullPath)) {
                throw new Exception("Template not found: {$templatePath}");
            }
            
            $content = file_get_contents($fullPath);
            $this->templatesCache[$templatePath] = $this->pq->wrapContent($content);
        }
        
        return clone $this->templatesCache[$templatePath];
    }

    public function modelPlayerCard($player) {
        $template = $this->loadTemplate('components/player_card.html');
        
        // Modeler l'en-tête avec le nom du joueur
        $template->find('.card-header h5')->text($player['name']);
        
        // Gérer l'image du joueur
        $playerImage = $template->find('.player-image');
        if (!empty($player['image_url'])) {
            $playerImage->html('<img src="' . $player['image_url'] . '" alt="' . $player['name'] . '" class="img-fluid">');
        } else {
            $playerImage->html('<div class="player-avatar">' . substr($player['name'], 0, 1) . '</div>');
        }
        
        // Mettre à jour les informations du joueur
        $template->find('.player-team')->text($player['team']);
        $template->find('.player-position')->text($player['position']);
        
        // Configurer les boutons d'action avec les IDs
        $template->find('.details-btn')->attr('data-id', $player['id']);
        $template->find('.edit-btn')->attr('data-id', $player['id']);
        $template->find('.delete-btn')->attr('data-id', $player['id']);
        
        return $template;
    }

    public function modelPlayerDetails($player) {
        $template = $this->loadTemplate('components/player_details.html');
        
        // Modeler les détails du joueur
        $template->find('.player-name')->text($player['name']);
        $template->find('.player-team')->text($player['team']);
        $template->find('.player-position')->text($player['position']);
        $template->find('.player-age')->text($player['age'] . ' ans');
        
        // Gérer l'image
        if (!empty($player['image_url'])) {
            $template->find('.player-image')->html(
                '<img src="' . $player['image_url'] . '" alt="' . $player['name'] . '" class="img-fluid rounded">'
            );
        } else {
            $template->find('.player-image')->html(
                '<div class="player-avatar">' . substr($player['name'], 0, 1) . '</div>'
            );
        }
        
        // Ajouter les statistiques
        if (!empty($player['stats'])) {
            $stats = json_decode($player['stats'], true);
            $statsContainer = $template->find('.player-stats');
            foreach ($stats as $stat => $value) {
                $statsContainer->append(
                    '<div class="stat-item" data-stat="' . $stat . '">
                        <strong>' . $stat . ':</strong> ' . $value . 
                    '</div>'
                );
            }
        }
        
        return $template;
    }

    public function modelModal($modalType, $data = []) {
        $template = $this->loadTemplate('modals/' . $modalType . '.html');
        
        switch ($modalType) {
            case 'player_details_modal':
                $template->find('.modal-title')->text('Détails du joueur');
                $template->find('.modal-dialog')->addClass('modal-lg');
                break;
                
            case 'add_player_modal':
                $template->find('.modal-title')->text('Ajouter un Joueur');
                $template->find('.modal-dialog')->addClass('modal-lg');
                $template->find('form')
                    ->addClass('needs-validation')
                    ->attr('novalidate', 'novalidate');
                break;
                
            case 'edit_player_modal':
                $template->find('.modal-title')->text('Modifier le Joueur');
                $template->find('.modal-dialog')->addClass('modal-lg');
                $template->find('form')
                    ->addClass('needs-validation')
                    ->attr('novalidate', 'novalidate');
                if (!empty($data)) {
                    foreach ($data as $field => $value) {
                        $template->find('[name="' . $field . '"]')->val($value);
                    }
                }
                break;
                
            case 'delete_confirmation_modal':
                $template->find('.modal-title')->text('Confirmer la suppression');
                $template->find('.modal-dialog')
                    ->removeClass('modal-sm')
                    ->addClass('modal-dialog-centered')
                    ->css('max-width', '300px');
                $template->find('.btn-danger')->text('Supprimer');
                if (!empty($data['name'])) {
                    $template->find('.confirmation-message')
                        ->text('Êtes-vous sûr de vouloir supprimer ' . $data['name'] . ' ?');
                }
                break;
        }
        
        return $template;
    }

    public function validatePlayerForm($data) {
        $errors = [];
        $form = $this->pq->wrapContent('<form></form>');
        
        // Validation des champs requis
        $requiredFields = ['name', 'team', 'position', 'age', 'nationality'];
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                $errors[] = "Le champ {$field} est requis";
            }
            
            $input = $this->pq->wrapContent('<input>');
            $input->find('input')
                ->attr('name', $field)
                ->attr('value', $data[$field] ?? '');
            $form->append($input);
        }
        
        // Validation de l'âge
        if (!empty($data['age'])) {
            if (!is_numeric($data['age']) || $data['age'] < 15 || $data['age'] > 45) {
                $errors[] = "L'âge doit être compris entre 15 et 45 ans";
            }
        }
        
        return [
            'isValid' => empty($errors),
            'errors' => $errors,
            'form' => $form
        ];
    }

    public function render($template, $data = []) {
        // Si le template est une chaîne, on l'utilise directement
        // Sinon, on charge le template depuis un fichier et on récupère son contenu HTML
        if (is_string($template)) {
            $content = $template;
        } else {
            $content = $this->loadTemplate($template);
            $content = $content->html();
        }
        
        // Parcours de toutes les données à injecter dans le template
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                // Cas des tableaux imbriqués : par exemple pour {{player.name}}, {{player.team}}
                // $key sera 'player' et $value sera ['name' => 'Zidane', 'team' => 'Real Madrid']
                foreach ($value as $subKey => $subValue) {
                    // On remplace {{player.name}} par 'Zidane'
                    $content = str_replace('{{'.$key.'.'.$subKey.'}}', $subValue, $content);
                }
            } else {
                // Cas des variables simples : par exemple {{team}} sera remplacé par 'Real Madrid'
                $content = str_replace('{{'.$key.'}}', $value, $content);
            }
        }
        
        // Suppression des variables non remplacées dans le template
        // Par exemple si {{age}} n'a pas été fourni dans $data, il sera supprimé
        // La regex /\{\{[^}]+\}\}/ trouve tous les motifs du type {{quelquechose}}
        $content = preg_replace('/\{\{[^}]+\}\}/', '', $content);
        
        return $content;
    }
} 