<?php
$formFields =
    [
        [
            'name' => 'name',
            'label' => 'Nom',
            'description' => 'Nom de la société',
            'value' => $information->get('name')
        ],
        [
            [
                'name' => 'address',
                'label' => 'Adresse',
                'description' => 'Adresse de la société',
                'value' => $information->get('address')
            ],
            [
                'name' => 'postalCode',
                'label' => 'Code postal',
                'description' => 'Code postal de la société',
                'value' => $information->get('postalCode')
            ],
            [
                'name' => 'city',
                'label' => 'Ville',
                'description' => 'Ville de la société',
                'value' => $information->get('city')
            ]
        ],
        [
            [
                'name' => 'mail',
                'label' => 'Email',
                'description' => 'Email affiché sur le site',
                'value' => $information->get('mail')
            ],
            [
                'name' => 'receptionMail',
                'label' => 'Email de réception',
                'description' => 'Email de réception des mails (si différent du mail affiché)',
                'value' => $information->get('receptionMail')
            ],
        ],
        [
            [
                'name' => 'phoneNumber',
                'label' => 'Numéro de téléphone',
                'description' => 'Numéro de téléphone affiché sur le site',
                'value' => $information->get('phoneNumber')
            ],
            [
                'name' => 'phoneNumberAlt',
                'label' => 'Numéro de téléphone secondaire',
                'description' => 'Second numéro de téléphone affiché sur le site',
                'value' => $information->get('phoneNumberAlt')
            ]
        ],
        [
            [
                'name' => 'gmap',
                'label' => 'Géolocalisation (iframe Google Maps)',
                'description' => 'Iframe provenant de Google Maps pour la géolocalisation de la société',
                'maxWidth' => '50%',
                'type' => 'textarea',
                'value' => $information->get('map')
            ]
        ]
    ];

$socialsFields = [
    [
        [
            'name' => 'facebook',
            'label' => 'Facebook',
            'description' => 'Lien vers la page Facebook',
            'value' => $information->get('facebook')
        ],
        [
            'name' => 'instagram',
            'label' => 'Instagram',
            'description' => 'Lien vers la page Instagram',
            'value' => $information->get('instagram')
        ],
        [
            'name' => 'analytics',
            'label' => 'Google Analytics',
            'description' => 'Identifiant Google Analytics',
            'value' => $information->get('analytics')
        ],
    ]
]
?>
<div class="pl-64 ">
    <div class="p-8 divide-y-2 divide-slate-100 flex flex-col">
        <div class="pb-4">
            <h2 class="text-xl font-bold leading-7 text-gray-900">Gestion des informations du site internet</h2>
        </div>

        <?php
        if (isset($successMessage)) {
            renderSuccessMessage($successMessage);
        }

        if (!empty($errorMessages)) {
            renderErrorMessages($errorMessages);
        }

        ?>

        <div class="py-8 flex flex-col">
            <form class="space-y-6 divide-y-2 divide-slate-100 flex flex-col" action="" method="POST">
                <?php foreach ($formFields as $field) {
                    renderFields($field);
                } ?>
                <h2 class="text-xl font-bold leading-7 text-gray-900 pt-4">Gestion des réseaux sociaux & partage de données</h2>
                <?php foreach ($socialsFields as $field) {
                    renderFields($field);
                } ?>
                <div class="pt-4">
                    <button type="submit" class="block min-w-max rounded-md bg-blue-600 px-[25px] py-[10px] text-center text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">Valider</button>
                </div>
            </form>
        </div>
    </div>
</div>