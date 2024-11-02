<?php
function renderFields($fields = [])
{
    // Vérification si le premier élément est un tableau, donc un groupe de champs
    if (is_array($fields) && isset($fields[0]) && is_array($fields[0])) {
?>
        <div class="pt-4 flex flex-row flex-wrap gap-4 w-full">
            <?php
            foreach ($fields as $subField) {
                renderFields($subField); // Appel récursif pour les sous-champs
            }
            ?>
        </div>
    <?php
    } elseif (is_array($fields)) {
        $maxWidth = $fields['maxWidth'] ?? null;

    ?>
        <div class="flex-auto min-w-[320px] <?= $maxWidth ? 'max-w-[50%]' : 'max-w-[380px]' ?>">
            <label for="<?= $fields['name'] ?>" class="block text-sm font-medium leading-6 text-gray-900"><?= $fields['label'] ?></label>
            <div class="mt-2">
                <?php
                if (isset($fields['options'])) {
                ?>
                    <select id="<?= $fields['name'] ?>"
                        name="<?= $fields['name'] ?>"
                        class="block resize-y w-full p-2 rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6"
                        <?= isset($fields['required']) && $fields['required'] ? 'required' : '' ?>>
                        <?php foreach ($fields['options'] as $key => $value) {
                        ?>
                            <option <?= $fields['value'] && $fields['value'] == $key ? 'selected' : '' ?> value="<?= $key ?>"><?= $value ?></option>
                        <?php
                        } ?>
                    </select>
                <?php
                } else if (isset($fields['classOptions'])) {
                ?>
                    <select id="<?= $fields['name'] ?>"
                        name="<?= $fields['name'] ?>"
                        class="block resize-y w-full p-2 rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6"
                        <?= isset($fields['required']) && $fields['required'] ? 'required' : '' ?>>
                        <option value="">Pas de page parente</option>
                        <?php foreach ($fields['classOptions'] as $page): ?>
                            <!-- Si la page a des enfants, on crée un optgroup -->
                            <?php if (!empty($page->get('children'))): ?>
                                <!-- Le parent est sélectionnable -->
                                <option value="<?= $page->get('id') ?>"><?= $page->get('name') ?></option>
                                <!-- Optgroup pour les enfants -->
                                <optgroup label="<?= $page->get('name') ?>">
                                    <?php foreach ($page->get('children') as $child): ?>
                                        <option value="<?= $child->get('id') ?>"><?= $child->get('name') ?></option>
                                    <?php endforeach; ?>
                                </optgroup>
                            <?php else: ?>
                                <!-- Si pas d'enfants, on affiche simplement l'option -->
                                <option value="<?= $page->get('id') ?>"><?= $page->get('name') ?></option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                <?php
                } else if (isset($fields['type']) && $fields['type'] === 'textarea') {
                ?>
                    <textarea
                        id="<?= $fields['name'] ?>"
                        name="<?= $fields['name'] ?>"
                        placeholder="<?= $fields['placeholder'] ?? '' ?>"
                        class="block min-h-32 resize-y w-full p-2 rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6"
                        <?= isset($fields['required']) && $fields['required'] ? 'required' : '' ?>><?= $fields['value'] ?? '' ?></textarea>
                <?php
                } else {
                ?>
                    <input
                        id="<?= $fields['name'] ?>"
                        name="<?= $fields['name'] ?>"
                        type="<?= $fields['type'] ?? 'text' ?>"
                        value="<?= $fields['value'] ?? '' ?>"
                        autocomplete="<?= $fields['autocomplete'] ?? '' ?>"
                        class="block w-full p-2 rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6"
                        placeholder="<?= $fields['placeholder'] ?? '' ?>"
                        <?= isset($fields['required']) && $fields['required'] ? 'required' : '' ?> />
                <?php
                }
                ?>
            </div>
            <p class="text-gray-400 text-xs font-medium leading-6 tracking-wide"><?= $fields['description'] ?></p>
        </div>
    <?php
    }
}

function validateFormData($formData, $filters)
{
    $errorMessages = [];
    $validatedData = [];

    foreach ($filters as $key => $filter) {
        $value = trim($formData[$key] ?? ''); // Récupérer la valeur et la nettoyer

        // Si un filtre est défini pour ce champ
        if (isset($filter['filter']) && $value != '') {
            $filteredValue = filter_var($value, $filter['filter']);

            // Si le filtre échoue, ajouter un message d'erreur
            if ($filteredValue === false) {
                $errorMessages[] = "Le champ '{$filter['label']}' n'est pas valide.";
            } else {
                // Stocker la valeur filtrée dans les données validées
                $validatedData[$key] = $filteredValue;
            }
        } else {
            // Si aucun filtre spécifique, assainir la chaîne
            $validatedData[$key] = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
        }
    }

    // Si des erreurs sont présentes, les retourner
    if (!empty($errorMessages)) {
        return ['errors' => $errorMessages];
    }

    // Si aucune erreur, retourner les données validées
    return ['success' => $validatedData];
}

function renderSuccessMessage($message = '')
{
    ?>
    <div class="alert alert-success bg-green-100 border-t-4 border-green-500 rounded-b text-green-900 px-4 py-3 text-sm font-normal" role="alert">
        <p><?= $message ?></p>
    </div>
<?php
}

function renderErrorMessages($messages = [])
{
    ob_start();
?>
    <ul class="alert alert-error bg-red-100 border-t-2 border-red-500 rounded-b text-red-900 px-4 py-3 text-sm font-normal" role="alert">
        <?php foreach ($messages as $error) { ?>
            <li><?= nl2br(htmlspecialchars($error)) ?></li>
        <?php } ?>
    </ul>
<?php
    return ob_get_clean();
}


?>