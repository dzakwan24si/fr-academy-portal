import os

auth_path = r'd:\ProjectFR\fr-academy-portal\app\Http\Controllers\AuthController.php'

if os.path.exists(auth_path):
    with open(auth_path, 'r', encoding='utf-8') as f:
        content = f.read()
    
    # Remove the orphaned */
    content = content.replace('} */', '}')
    
    with open(auth_path, 'w', encoding='utf-8', newline='') as f:
        f.write(content)
        
print("Fixed syntax error in AuthController.php")
