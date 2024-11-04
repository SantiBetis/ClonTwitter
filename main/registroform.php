<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="flex items-center justify-center min-h-screen bg-gray-100">
    <div class="w-full max-w-md p-6 bg-white rounded-lg shadow-md">
        <img src="../img/logo2.png" alt="Logo" class="w-16 h-16 mx-auto mb-4" />
        <h1 class="text-2xl font-semibold text-center text-gray-800 mb-6">Regístrate</h1>
        <form action="../scripts/registro.php" method="POST" class="space-y-4">
            <div>
                <label for="username" class="block text-sm font-medium text-gray-700">Username:</label>
                <input type="text" id="username" name="username" required 
                       class="w-full px-4 py-2 mt-1 border border-gray-300 rounded-md focus:border-blue-500 focus:ring focus:ring-blue-200" />
            </div>
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email:</label>
                <input type="email" id="email" name="email" required 
                       class="w-full px-4 py-2 mt-1 border border-gray-300 rounded-md focus:border-blue-500 focus:ring focus:ring-blue-200" />
            </div>
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Password:</label>
                <input type="password" id="password" name="password" required 
                       pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" 
                       title="Debe contener al menos un número y una mayúscula y una minúscula, y al menos 8 o más carácteres"
                       class="w-full px-4 py-2 mt-1 border border-gray-300 rounded-md focus:border-blue-500 focus:ring focus:ring-blue-200" />
            </div>
            <div>
                <input type="submit" value="Regístrate" name="submit"
                       class="w-full py-2 mt-4 font-medium text-white rounded-md"
                       style="background-color: #1da1f2;" />
            </div>
        </form>
        <div class="mt-4 text-center text-sm text-gray-600">
            <p>¿Ya tienes cuenta? 
               <a href="../index.php" class="hover:underline" style="color: #1da1f2;">Inicia sesión</a>
            </p>
        </div>
    </div>
</body>
</html>
