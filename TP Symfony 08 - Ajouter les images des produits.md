# Projet SIO-Shoes : site « e-commerce » avec Symfony

## Partie 8 : ajouter les images des produits

### 1. Ajouter des images dans les produits

Modifiez l'entité « Product » en exécutant la commande :

```bash
symfony console make:entity Product
```

Ajoutez un champ « image » de type « string ». On acceptera la valeur NULL (un produit n'a pas forcément d'image) :

```
PS C:\Symfony\e-commerce> symfony console make:entity Product
 Your entity already exists! So let's add some new fields!

 New property name (press <return> to stop adding fields):
 > image

 Field type (enter ? to see all types) [string]:
 >

 Field length [255]:
 >

 Can this field be null in the database (nullable) (yes/no) [no]:
 > yes

 updated: src/Entity/Product.php

 Add another property? Enter the property name (or press <return> to stop adding fields):
 >

  Success!

 Next: When you're ready, create a migration with symfony.exe console make:migration
```

### 2. Préparez et exécutez la migration pour mettre à jour la BDD

```bash
symfony console make:migration
symfony console doctrine:migrations:migrate
```

### 3. Ajouter le champ « image » dans le formulaire « src\Form\ProductForm.php »

Importez les deux bibliothèques « FileType » et « File » :

```php
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;
```

Modifiez ensuite la fonction « BuildForm » :

```php
public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('description')
            ->add('price')
            ->add('image',FileType::class,[
                'label'=>'Image du produit :',
                'mapped' => false,
                'required'=>false,
                'constraints'=>[
                    new File([
                        "maxSize"=>"1024k",
                        'extensions'=> ['jpg','png','jpeg'],
'extensionsMessage'=> 'Votre image doit être dans un format valide (.jpg, .png, .jpeg) !',
'maxSizeMessage'=>'Votre image doit faire moins de 1024k !'
                    ])
                ]
            ])

            ->add('subCategories', EntityType::class, [
                'class' => SubCategory::class,
                'choice_label' => 'name',
                'multiple' => true,
            ])
        ;
    }
```

### 4. Modifiez la vue « templates\product\_form.html.twig » pour afficher le champ « image »

```twig
{{ form_start(form) }}

    <label for="">Nom du produit : </label>
{{ form_widget(form.name,{'attr':{'class':'form form-control',
'placeholder':"nom"}}) }}
    <br>
    <label for="">Description : </label>
    {{ form_widget(form.description,{'attr':{'class':'form form-control'}}) }}
    <br>
    <label for="">Prix : </label>
    {{ form_widget(form.price,{'attr':{'class':'form form-control'}}) }}
    <br>
    <label for="">Sous-catégories : </label>
    {{ form_widget(form.subCategories,{'attr':{'class':'form form-control'}}) }}
    <br>
    <label for="">Image : </label>
    {{ form_widget(form.image,{'attr':{'class':'form form-control'}}) }}
    <br>

    <button class="btn btn-primary">{{ button_label|default('Enregistrer')
}}</button>
{{ form_end(form) }}
```

### 5. Modifiez le fichier « config\services.yaml » pour ajouter un nouveau paramètre

```yaml
parameters:
image_dir: '%kernel.project_dir%/public/uploads/images'
```

Nous utiliserons le paramètre « image_dir » pour indiquer le chemin du répertoire où les images vont être sauvegardées.

### 6. Modifiez le contrôleur « src\Controller\ProductController.php » pour enregistrer l'image dans la fonction « new() »

Commencez par importer la bibliothèque « SluggerInterface » :

```php
use Symfony\Component\String\Slugger\SluggerInterface;
```

Modifiez le code de la fonction « new() » :

```php
public function new(Request $request, EntityManagerInterface $entityManager,
SluggerInterface $slugger): Response
{
    $product = new Product();
    $form = $this->createForm(ProductForm::class, $product);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
          $image = $form->get('image')->getData();
          if ($image){
                $originalName = pathinfo($image->getClientOriginalName(),
                PATHINFO_FILENAME);
                $safeFileName = $slugger->slug($originalName);
                $newFileName = $safeFileName.'-'.uniqid().'.'.$image->
                guessExtension();

                try {
                      $image->move(
                            $this->getParameter('image_dir'),
                            $newFileName
                      );
                }
                catch(FileException $exception){

                }
                $product->setImage($newFileName);
          }

          $entityManager->persist($product);
          $entityManager->flush();

          $this->addFlash('success','Le produit a été ajouté');

          return $this->redirectToRoute('app_product_index', [],
          Response::HTTP_SEE_OTHER);
    }

    return $this->render('product/new.html.twig', [
            'product' => $product,
            'form' => $form,
    ]);
}
```

## TRAVAIL A FAIRE POUR LES ETUDIANTS

> - Modifier la route « /edit » du contrôleur « ProductController.php » pour permettre la modification de l'image d'un produit.
