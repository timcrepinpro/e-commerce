# Projet SIO-Shoes : site « e-commerce » avec Symfony

## Partie 2 : formulaire d'inscription et de connexion

### 1. Créer le système d'authentification

Exécutez la commande suivante (appuyez sur la touche « entrée » pour choisir les valeurs par défaut) :

```bash
symfony console make:user
```

Questions posées (valeurs par défaut) :

- The name of the security user class : `User`
- Do you want to store user data in the database (via Doctrine)? : `yes`
- Enter a property name that will be the unique "display" name for the user : `email`
- Does this app need to hash/check user passwords? : `yes`

Résultat attendu :

```
created: src/Entity/User.php
created: src/Repository/UserRepository.php
updated: src/Entity/User.php
updated: config/packages/security.yaml

Success!
```

### 2. Préparer la migration pour créer la table user dans la BDD

```bash
symfony console make:migration
```

**Attention :** vérifiez que la version de MariaDB est bien celle déclarée dans le fichier « .env ».

Résultat attendu :

```
created: migrations/Version20250723102921.php

Success!

Review the new migration then run it with symfony.exe console doctrine:migrations:migrate
```

### 3. Exécuter la migration avec Doctrine

```bash
symfony console doctrine:migration:migrate
```

Répondre `yes` à l'avertissement (« You are about to execute a migration in database ... »).

Résultat attendu :

```
[OK] Successfully migrated to version: DoctrineMigrations\Version20250723102921
```

La table `user` a été créée dans la BDD, ainsi que deux nouvelles tables. La table « doctrine_migration_versions » permet de visualiser les migrations effectuées.

### 4. Création du formulaire de connexion

```bash
symfony console make:security:form-login
```

Questions posées (valeurs par défaut) :

- Choose a name for the controller class : `SecurityController`
- Do you want to generate a '/logout' URL? : `yes`
- Do you want to generate PHPUnit tests? : `no`

Résultat attendu :

```
created: src/Controller/SecurityController.php
created: templates/security/login.html.twig
updated: config/packages/security.yaml

Success!
```

**Attention :** `symfony console make:auth` est déprécié !

### 5. Ajouter un formulaire d'inscription

```bash
symfony console make:registration-form
```

Questions posées :

- Do you want to add a `#[UniqueEntity]` validation attribute to your User class ? : `yes` (défaut)
- Do you want to send an email to verify the user's email address after registration? : **`no`**
- Do you want to automatically authenticate the user after registration? : `yes` (défaut)
- Do you want to generate PHPUnit tests? : `no` (défaut)

Résultat attendu :

```
updated: src/Entity/User.php
created: src/Form/RegistrationForm.php
created: src/Controller/RegistrationController.php
created: templates/registration/register.html.twig

Success!

Next:
Make any changes you need to the form, controller & template.
Then open your browser, go to "/register" and enjoy your new form!
```

### 6. Créer un utilisateur et vérifier avec le profiler

Ouvrez la page `localhost:8000/register` et créez un utilisateur.

Observez le résultat dans le « profiler » de Symfony. Allez dans le bas de la page et vérifiez que l'utilisateur créé est bien connecté :

- Logged in as : *l'email de l'utilisateur*
- Authenticated : Yes
- Roles : `ROLE_USER`
- Token class : `UsernamePasswordToken`
- Firewall name : `main`

### 7. Ajouter le nom et le prénom dans le formulaire d'inscription

Allez dans `src/Form/RegistrationForm.php` et ajoutez les champs `firstName` et `lastName` :

```php
public function buildForm(FormBuilderInterface $builder, array $options): void
{
    $builder
        ->add('email')
        ->add('agreeTerms', CheckboxType::class, [
            'mapped' => false,
            'constraints' => [
                new IsTrue([
                    'message' => 'You should agree to our terms.',
                ]),
            ],
        ])
        ->add('firstName')
        ->add('lastName')
        // ...
```

### 8. Modifier l'entité « User »

```bash
symfony console make:entity User
```

L'entité existe déjà, on ajoute donc de nouveaux champs :

| Property name | Field type | Field length | Nullable |
|---------------|------------|--------------|----------|
| `firstName`   | `string` (défaut) | `255` | `no` (défaut) |
| `lastName`    | `string` (défaut) | `255` | `no` (défaut) |

Appuyez sur « entrée » à la question suivante pour arrêter l'ajout de champs.

### 9. Préparer la migration pour ajouter les champs « firstName » et « lastName »

```bash
symfony console make:migration
```

### 10. Exécuter la migration avec Doctrine

```bash
symfony console doctrine:migration:migrate
```

### 11. Modifier la vue pour afficher les champs « firstName » et « lastName »

Allez dans `templates/registration/register.html.twig` et ajoutez les 2 champs (lignes `firstName` et `lastName`) :

```twig
{% extends 'base.html.twig' %}

{% block title %}Register{% endblock %}

{% block body %}
    <h1>Inscription</h1>
    {{ form_errors(registrationForm) }}
    {{ form_start(registrationForm) }}
        {{ form_row(registrationForm.email) }}
        {{ form_row(registrationForm.firstName) }}
        {{ form_row(registrationForm.lastName) }}
        {{ form_widget(registrationForm.plainPassword, {label: 'Password'}) }}
        {{ form_row(registrationForm.agreeTerms) }}
        <button type="submit" class="btn">Register</button>
    {{ form_end(registrationForm) }}
{% endblock %}
```
