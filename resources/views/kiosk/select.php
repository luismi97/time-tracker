<div class="bg-white dark:bg-slate-900 shadow-lg rounded-xl p-8">
    <div class="text-center mb-6">
        <h1 class="text-2xl font-bold text-slate-800 dark:text-slate-100"><?= e(site_name()) ?></h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Marcar entrada / salida</p>
    </div>

    <form method="GET" action="/kiosk" class="space-y-4">
        <div>
            <label for="company" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Codigo de empresa</label>
            <input type="text" id="company" name="company" required autofocus placeholder="Ej: mi-empresa"
                   class="mt-1 block w-full rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-center text-lg">
            <p class="mt-1 text-xs text-slate-400 dark:text-slate-500">Lo encuentras en Configuracion &rarr; Registro de horas, o pideselo a tu administrador.</p>
        </div>
        <button type="submit" class="w-full rounded-lg bg-indigo-600 px-4 py-2.5 text-white font-semibold hover:bg-indigo-700 transition">
            Continuar
        </button>
    </form>
    <p class="mt-6 text-center text-xs text-slate-400 dark:text-slate-500">
        <a href="/login" class="hover:text-indigo-600 dark:hover:text-indigo-400">Iniciar sesion en su lugar</a>
    </p>
</div>
