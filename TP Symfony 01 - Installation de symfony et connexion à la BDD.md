# Projet SIO-Shoes : site « e-commerce » avec Symfony

## Partie 1 : installation de Symfony et connexion à la BDD

### 1. Installer Symfony

Installez d'abord Composer puis Symfony CLI (avec Scoop) : <https://symfony.com/doc/current/setup.html>

### 2. Créer une nouvelle application Symfony

Désactivez si nécessaire l'antivirus. Choisissez un nom pour votre projet (ici « e-commerce »).
Ouvrez une fenêtre de commande et exécutez la commande :

```bash
symfony new e-commerce --version="7.2.x" --webapp
```

> **Note :** tapez plutôt `"6.4"` pour la version.

### 3. Gérer l'accès au serveur de BDD et paramétrer le nom de la BDD

Nous allons utiliser XAMPP et créer une base de données « **my_shop_dev** ».
Commencez par ouvrir le projet dans Visual Studio Code et modifiez le fichier « .env » :

```env
DATABASE_URL="mysql://root:passwd@localhost:3306/my_shop_dev?serverVersion=10.4.32-MariaDB&charset=utf8mb4"
```

> **Note :** ne pas taper `passwd`, cela doit donner `...root:@localhost...`

**Remarque :** la version de MariaDB doit correspondre à celle installée sur votre serveur de BDD (XAMPP par exemple). Allez dans le répertoire `C:\Xampp\mysql\bin` puis exécutez la commande suivante pour vérifier :

```powershell
.\mysql --version
```

Exemple de résultat :

```
C:\xampp\mysql\bin\mysql.exe  Ver 15.1 Distrib 10.4.32-MariaDB, for Win64 (AMD64), source revision c4143f909528e3fab0677a28631d10389354c491
```

### 4. Créer la BDD avec Doctrine

```bash
symfony console doctrine:database:create
```

**Remarque :** on peut supprimer une BDD en utilisant la commande :

```bash
symfony console doctrine:database:drop
```

### 5. Créer un contrôleur et une vue pour la page d'accueil

```bash
symfony console make:controller HomeController
```

Résultat attendu :

```
Do you want to generate PHPUnit tests? [Experimental] (yes/no) [no]:
>

created: src/Controller/HomeController.php
created: templates/home/index.html.twig

Success!

Next: Open your new controller class and add some pages!
```

On peut voir le code généré dans `src/Controller/HomeController.php` :

```php
#[Route('/home', name: 'app_home')]
public function index(): Response
{
    return $this->render('home/index.html.twig', [
        'controller_name' => 'HomeController',
    ]);
}
```

On modifie le contrôleur pour que la page devienne la page par défaut (« / ») :

```php
#[Route('/', name: 'app_home')]
public function index(): Response
{
    return $this->render('home/index.html.twig', [
        'controller_name' => 'HomeController',
    ]);
}
```

Pour vérifier le fonctionnement, lancez le serveur en arrière-plan avec la commande suivante :

```bash
symfony serve -d
```

Vous pouvez aussi utiliser le mode interactif avec la commande :

```bash
symfony server:start
```

Résultat attendu :

```
[OK] Web server listening
     The Web server is using PHP CGI 8.2.12
     http://127.0.0.1:8000
```

Lancez le navigateur et allez sur la page `localhost:8000`.

Vous devez voir la page « **Hello HomeController!** ✅ » indiquant :

- Your controller at `.../e-commerce/src/Controller/HomeController.php`
- Your template at `.../e-commerce/templates/home/index.html.twig`
