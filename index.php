<?php
require_once 'AnalisadorLexico.php';

$codigoInput = $_POST['codigo'] ?? '';
$tokens = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty(trim($codigoInput))) {
    $lexico = new AnalisadorLexico($codigoInput);
    $tokens = $lexico->analisar();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analisador Léxico PHP</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen font-sans text-gray-800">

    <header class="bg-indigo-600 text-white p-4 shadow-md">
        <div class="container mx-auto">
            <h1 class="text-xl font-bold">Autômato Finito - Analisador Léxico PHP</h1>
        </div>
    </header>

    <main class="container mx-auto p-6 mt-6">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            
            <div class="bg-white p-6 rounded-lg shadow border border-gray-200 flex flex-col">
                <form method="POST" action="" class="flex flex-col flex-grow">
                    <textarea 
                        name="codigo" 
                        class="w-full h-80 p-4 font-mono text-sm bg-gray-900 text-green-400 rounded focus:outline-none focus:ring-2 focus:ring-indigo-500 resize-none flex-grow" 
                        spellcheck="false"
                        required><?= htmlspecialchars($codigoInput) ?></textarea>
                    
                    <button type="submit" class="mt-4 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded transition duration-200 self-end">
                        Analisar
                    </button>
                </form>
            </div>

            <div class="bg-white rounded-lg shadow border border-gray-200 overflow-hidden flex flex-col h-[450px] lg:h-auto">
                <div class="p-4 bg-gray-100 border-b border-gray-200">
                    <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wider">Tabela de Símbolos</h2>
                </div>
                
                <div class="overflow-y-auto flex-grow p-4">
                    <?php if (!empty($tokens)): ?>
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="text-xs text-gray-500 border-b border-gray-300">
                                    <th class="pb-2 font-medium">Lexema</th>
                                    <th class="pb-2 font-medium text-right">Token</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($tokens as $token): ?>
                                    <tr class="border-b border-gray-100 hover:bg-indigo-50 transition-colors">
                                        <td class="py-2 font-mono text-sm text-indigo-800">
                                            <?= htmlspecialchars($token['valor']) ?>
                                        </td>
                                        <td class="py-2 text-xs text-right text-gray-600">
                                            <span class="inline-block bg-gray-200 rounded px-2 py-1 font-semibold">
                                                <?= htmlspecialchars($token['tipo']) ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </main>

</body>
</html>