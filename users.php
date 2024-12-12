<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des utilisateurs</title>
    <link rel="stylesheet" href="style.css">
    <script>
        
        async function loadUsers() {
            try {
                const response = await fetch('get_users.php');
                const users = await response.json(); 
                
                const tbody = document.querySelector('tbody');
                tbody.innerHTML = ''; 

                if (users.length > 0) {
                    users.forEach(user => {
                        const row = `
                            <tr>
                                <td>${user.firstName}</td>
                                <td>${user.lastName}</td>
                                <td>${user.email}</td>
                                
                            </tr>
                        `;
                        tbody.innerHTML += row;
                    });
                } else {
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="4">Aucun utilisateur trouvé.</td>
                        </tr>
                    `;
                }
            } catch (error) {
                console.error('Erreur lors du chargement des utilisateurs :', error);
            }
        }

        

        
        // Charger les utilisateurs lorsque la page est prête
        document.addEventListener('DOMContentLoaded', loadUsers);
    </script>
    <style>
        /* Style de base */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f9;
            color: #333;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }

        h2 {
            font-size: 2em;
            color: #555;
            margin-bottom: 20px;
        }

        /* Style du tableau */
        table {
            width: 80%;
            border-collapse: collapse;
            margin: 20px 0;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
        }

        thead {
            background-color: #007BFF;
            color: white;
            text-transform: uppercase;
        }

        thead th {
            padding: 12px 15px;
            text-align: left;
        }

        tbody tr {
            border-bottom: 1px solid #ddd;
        }

        tbody tr:hover {
            background-color: #f1f1f1;
        }

        tbody td {
            padding: 12px 15px;
        }

      

       
    
    </style>
</head>
<body>

    <h2>Gestion des utilisateurs</h2>
   

    
    <table>
        <thead>
            <tr>
                <th>Prénom</th>
                <th>Nom</th>
                <th>Email</th>
                
            </tr>
        </thead>
        <tbody>
            <!-- Les données seront insérées ici par JavaScript -->
        </tbody>
    </table>
</body>
</html>
