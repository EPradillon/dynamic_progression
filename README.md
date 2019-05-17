# Description

Ajoute de nouveaux tag à ContactForm 7 qui permettent de se souvenir de leur dernière valeur pour un utilisateur et un formulaire donné.



# Gestion des questionnaires

 Depuis le menu admin de WordPress "Contact"
  
## Création d'un questionnaire dynamique "from scratch" 
  

<details>
<summary>Création d'un nouveau questionnaire "pas à pas".</summary>   

  
   
1. Choix d'un titre explicite, il ne sera pas lu par sur le site-web.  

```
TITRE : 2020-Partie0-Identification. 
```

2. Les inputs nécéssaires.  
On remplace les champs proposés par :
```html
[dynamichidden your-name "CF7_get_current_user key='user_login'"]
[dynamichidden your-email "CF7_get_current_user key='user_email'"]
[dynamichidden your-subject "2020-Partie0-Identification"]
  
[submit "Envoyer"]
```
Les champs your-name / your-email / your-subject permettent au plugin flamingo de rattacher un formulaire à un utilisateur depuis 
la partie admin de WordPress.
Pour éviter à l'utilisateur d'avoir à systématiquement renseigner ces champs, il seront dynamiquement rempli sur la base des informations saisies lors
de l'inscription sur le site.   
Ces inputs "hidden" seront cachés (pour les anglophobes) et l'utilisateur aura directement accès au contenu du formulaire.

3. Pour ajouter des champs dans le formulaire.  
On retrouva des syntaxes de champs comme ceci :  
```html
[dynamichidden your-name "CF7_get_current_user key='user_login'"]
[dynamichidden your-email "CF7_get_current_user key='user_email'"]
[dynamichidden your-subject "2020-Partie0-Identification"]

A quoi ressemble un champ texte?
[dynamictext P1_101 "dynamicProgression_get_answer question=P1_101"]

A quoi ressemble un champ Oui/Non? 
[dynamic_checkbox P1_102 use_label_element exclusive "Oui" "Non"]
  
[submit "Envoyer"]
```
> cf  "Les nouveaux tags dynamiques" de ce doc.

La sauvegarde de ce formulaire nous permet l'utilisation du "shortcode" au sein des pages/articles de Wordpress
qui affichera un formulaire à l'utilisateur visisant le site.  
</details>     

---

> Puisqu'un long questionnaire sera souvent décomposé en formulaires successifs, le choix des titres peut inclure de la taxonomie (ou taxinomie)
> 2020-Partie0-Identification  
> 2020-Partie1-Stratégie-Mutualisation  
> 2020-Partie2-Formats-Ouvert  
> 2020-Partie3-Logiciels--Systèmes-Libres 
>
> Cette nomenclature ordonne les formulaires par années puis par parties.  
> Les titres des questionnaires ne seront pas visibles par l'utilisateur.


## Les nouveaux tags dynamiques pour Contact Form 7


Bien qu'accessible via le générateur de balise du plugin, il aussi possible de copier une balise pour s'appuyer sur sa structure.


**Pour une réponse courte**
   
<details>
<summary>Dynamic text</summary>  

La balise insérée sera du style : **[dynamictext P1_Q001 id:P1_Q001 class:tnl_2020 "dynamicProgression_get_answer question=P1_Q001"]**  
Liste des attributs de la balise dans l'ordre : **[CF7tag / Nom / Attribut id: / Attribut class: / "Dynamic value"]**  
- **Nom** : Le nom du champ doit être unique. Pour éviter les redondances on peut nommer les champs comme ceci :  
*partie* _ *question*   
ex : P1_Q001 ( Pour la première question de la première partie )

- **Dynamic value** : pour remplacer la notion de "valeur par défault" et remplir le champ avec la potentielle  
précédente réponse de l'utilisateur il faut ici utiliser un "shortcode" (sans ses crochets) :    
dynamicProgression_get_answer question=**Nom**  
Pour retourner la réponse à la question nommé P1_Q001 :    
dynamicProgression_get_answer question=P1_Q001

- **Attribut id** : Peut être laisser vide ou prendre la valeur du nom (doit être unique).

- **Attribut class** : Sert de taxonomie, on pourra attribuer des comportements spécifiques à tous les champs d'une même class  
( par exemple tnl_2020 )



> On peut copier cette balise pour répondre à une question du même type en modifiant méticuleusement chaque occurence du nom de la question :  
> [dynamictext P1_**002** id:P1_**002** class:tnl_2020 "dynamicProgression_get_answer question=P1_**002**"]
</details>  

---  

**Proposer de répondre avec des cases à cocher**
  
<details>
<summary>Progression checkbox</summary>  

La balise insérée sera du style : **[dynamic_checkbox P5_501 id:P5_501 use_label_element exclusive "Oui" "Non"]**  

- **Nom** : Le nom du champ doit être unique. Pour éviter les redondances on peut nommer les champs comme ceci :  
*partie* _ *question*  
ex : P5_501 ( Pour la première question du questionnaire d'itentification ).

- **Options** : liste des options.  => "Oui" "Non" "peut-être"

- **Libéllé puis case** : Inverser la position des cases à cocher et du texte (je déconseille).

- **Entourer chaque élément avec un libellé** : conseillé, l'utilisateur peut cliquer sur le texte pour cocher la case. => use_label_element

- **Rendre les cases à cocher exclusives** : Une seule option pourra être séléctionnée (ex yes/no). => exclusive

- **Attribut id** : Peut être laisser vide ou prendre la valeur du nom (doit être unique).

- **Attribut class** : Sert de taxonomie, on pourra attribuer des comportements spécifiques à tous les champs d'une même class  
( par exemple tnl_2020 ).

> On peut copier cette balise pour répondre à une question du même type en modifiant méticuleusement le numéro de la question et les options si elles changent:  
> [dynamic_checkbox P5_502 id:P5_502 use_label_element exclusive "Oui" "Non" "peut-être"]
</details>

---

**Pour une réponse longue**
  
<details>
<summary>Progression text area</summary>  

La balise insérée sera du style : **[progression_textarea P5_516 id:P5_516 "dynamicProgression_get_answer question=P5_516"]**  

- **Nom** : Le nom du champ doit être unique. Pour éviter les redondances on peut nommer les champs comme ceci :  
*partie* _ *question*   
ex : P1_Q001 ( Pour la première question de la première partie )

- **Dynamic value** : pour remplacer la notion de "valeur par défault" et remplir le champ avec la potentielle  
précédente réponse de l'utilisateur il faut ici utiliser un "shortcode" (sans ses crochets) :    
dynamicProgression_get_answer question=**Nom**  
Pour retourner la réponse à la question nommé P1_Q001 :    
dynamicProgression_get_answer question=P1_Q001

- **Attribut id** : Peut être laisser vide ou prendre la valeur du nom (doit être unique).

- **Attribut class** : Sert de taxonomie, on pourra attribuer des comportements spécifiques à tous les champs d'une même class  
( par exemple tnl_2020 )

> On peut copier cette balise pour répondre à une question du même type en modifiant méticuleusement chaque occurence du nom de la question :  
> [progression_textarea P2_219 id:P2_219 "dynamicProgression_get_answer question=P2_219"]
</details>

---

**Pour un menu déroulant**
  
<details>
<summary>Progression select</summary>  

La balise insérée sera du style : **[dynamic_select P0_015 class:tnl_2020 dynamic_value "Ville" "EPCI" "Département" "Région" "Centre De Gestion" "SDIS" "Syndicat" "Autre"]**  

- **Nom** : Le nom du champ doit être unique. Pour éviter les redondances on peut nommer les champs comme ceci :  
*partie* _ *question*   
ex : P1_Q001 ( Pour la première question de la première partie )

- **Options** : liste des options.  => "Ville" "EPCI" "Département" "Région" "Centre De Gestion" "SDIS" "Syndicat" "Autre"

- **Selections multiples** : Fonctionnement étrange... je ne l'ai jamais vu car autant utiliser les checkbox qui sont faites pour.

- **dynamic value as first option** : obligatoire (j'ai choisi de laisser "l'option" pour garder une continuité avec la structure de ContactForm7) 
mais pour avoir la récupération de l'ancienne réponse il faut cocher cette case. => dynamic_value

- **Attribut id** : Peut être laisser vide ou prendre la valeur du nom (doit être unique).

- **Attribut class** : Sert de taxonomie, on pourra attribuer des comportements spécifiques à tous les champs d'une même class  
( par exemple tnl_2020 )

> On peut copier cette balise pour répondre à une question du même type en modifiant méticuleusement le nom de la question :  
> [dynamic_select P0_010 class:tnl_2020 dynamic_value "Ville" "EPCI" "Département" "Région" "Centre De Gestion" "SDIS" "Syndicat" "Autre"]
</details>

---

#### Conditional fields Group

When creating a conditional form group remember that a javascript function
from this plugin will filter the posted value.
Every input supposed to be hidden  when submiting the form will be clear.
This functionality is meant to prevent the post of one "if no :" question's field
when the user finally decided to check the "yes" option.


#### Conditional fields Group

La création d'un "Conditional fields Group" entraine la manifestation d'une fonction 
javascript de ce plugin au moment de sauvegarder/poster l'étape du formulaire.
Si ce groupe est supposé être masqué tout champ sera vidé ou deselectionné lorsque
l'utilisateur sauvergarde l'avancer de son formulaire.
(Cette fonctionnalité a pour objectif d'éviter l'envoi de réponse à une question "si non : "
lorsque l'utilisasteur a finalement opté pour le oui