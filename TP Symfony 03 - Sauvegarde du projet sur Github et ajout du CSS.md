# Projet SIO-Shoes : site « e-commerce » avec Symfony

## Partie 3 : sauvegarde du projet sur Github et ajout du CSS

### 1. Enregistrement du projet sur Github

- Créez un repository sur Github (« sio-shoes », par exemple). Ouvrez une fenêtre ligne de commande et allez dans le répertoire de votre projet.
- Générez une base locale dans le répertoire de travail :
  ```bash
  git init
  ```
- Vérifiez le statut du projet local :
  ```bash
  git status
  ```
- Mettez à jour l'index :
  ```bash
  git add .
  ```
- Exécutez un commit :
  ```bash
  git commit -am « Formulaires d'inscription et de connexion »
  ```
- Renommez votre branche locale master en main :
  ```bash
  git branch -M main
  ```
- Ajoutez le dépôt distant :
  ```bash
  git remote add origin https://github.com/dubromelle/sio-shoes.git
  ```
- Envoyez les modifications locales sur le repository Github :
  ```bash
  git push origin main
  ```

> **Remarque :** pour récupérer le projet à partir du repository, il faut effectuer la commande
> ```bash
> git clone https://github.com/dubromelle/sio-shoes.git
> ```
> Puis aller dans le répertoire du projet et lancer la commande : `composer install`

### 2. Commencer la mise en forme

La feuille de style principale du projet se trouve dans `assets\styles\app.css`.
Vous pouvez modifier le fond d'écran et actualiser une page de votre application pour voir le changement. La base des pages HTML se trouve dans le fichier « `templates\base.html.twig` ».

### 3. Utilisation de « Bootstrap »

Allez sur https://getbootstrap.com/ puis copiez le lien « Include via CDN ».
Copiez le code dans l'entête (`<head>`) du fichier « `templates\base.html.twig` ».

Si vous souhaitez utiliser un fichier .css local, enregistrez bootstrap.min.css et copiez-le dans le répertoire public/assets/css (créez les sous-répertoires si besoin). Ajoutez ensuite le code suivant dans l'entête (`<head>`) du fichier `templates\base.html.twig` :

```twig
<link rel="stylesheet " href="{{ asset('assets/css/bottstrap.min.css') }}">
```

### 4. Utilisation de la balise meta viewport pour contrôler la mise en page sur mobile

Notre site doit fonctionner sur tous les supports, y compris les mobiles et les tablettes.
Copiez le code suivant dans l'entête (`<head>`) du fichier « `templates\base.html.twig` » :

```html
<meta name="viewport" content="width=device-width, initial-scale=1">
```

### 5. Mise en forme du formulaire de connexion

Créez deux nouvelles classes `login_form` et `btn_block` dans « `assets\styles\app.css` » :

```css
body {
    background-color: whitesmoke;
}

.login-form {
    width: 20em;
}

.btn-block {
    width: 100%;
}

.security-form{
    display:flex;
    align-items: center;
    justify-content: space-around;
    width: 100%;
    height: 100%;
}
```

Allez dans le bloc body du fichier `templates\security\login.html.twig` et entourez le formulaire par une balise `<div class = "container security-form"> … </div>` puis appelez la classe `login_form` dans la balise `<Form>` et la classe `btn-block` dans la balise `<button>` :

```twig
{% block body %}
    <div class = "container security-form">
        <form method="post" class="login-form">
            {% if error %}
                …
            <button class="btn btn-lg btn-primary btn-block" type="submit">
                Sign in
            </button>
        </form>
    </div>
{% endblock %}
```

### 6. Mise en forme du formulaire d'inscription

Modifiez le code du fichier « `templates\registration\register.html.twig` » :

```twig
{% extends 'base.html.twig' %}

{% block title %}Register{% endblock %}

{% block body %}
    <div class = "container security-form">
        <div class "login-form">
            <h1>Inscription</h1>
            {{ form_errors(registrationForm) }}

            {{ form_start(registrationForm) }}
                {{ form_widget(registrationForm.email,{'attr':{'class':'form form-control','placeholder':"email"}}) }}
                <br>
                {{ form_widget(registrationForm.firstName,{'attr':{'class':'form form-control','placeholder':"prénom"}}) }}
                <br>
                {{ form_widget(registrationForm.lastName,{'attr':{'class':'form form-control','placeholder':"nom"}}) }}
                <br>
                {{ form_widget(registrationForm.plainPassword,{'attr':{'class':'form form-control','placeholder':"mot de passe"}} ) }}
                <br>
                <label for="">J'accepte les conditions d'utilisation :&nbsp;</label>
                {{ form_widget(registrationForm.agreeTerms) }}
                <br>
                <br>
<button type="submit" class="btn btn-primary btn-block">S'inscrire</button>
            {{ form_end(registrationForm) }}
        </div>
    </div>
{% endblock %}
```

> **Remarque :** `form_widget` permet de ne pas afficher le label. On peut afficher un label directement en Html si on le souhaite.
