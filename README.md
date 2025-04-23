# 📁 Proyecto Backend - Sistema de Gestión Documental

Este proyecto está desarrollado en **Laravel** con interfaz administrativa construida en **FilamentPHP**.  
Permite registrar, categorizar y consultar documentos digitalizados, ideal para procesos internos de organizaciones o entidades públicas.

---

## 🧰 Requisitos

- PHP 8.2 (recomendado usar XAMPP)
- Composer
- MySQL
- Laravel 11+
- Filament 3+

---

## ⚙️ Instalación local (XAMPP)

### 1. Clonar el repositorio
```bash
git clone https://github.com/erickvnrm/gestor-documental.git
```
### 2. Generar la clave de la app
```bash
php artisan key:generate
```
### 3. Migrar tablas y Seeders
```bash
php artisan key:generate
php artisan db:seed
```
### 4. 🚀 Iniciar la aplicación
```bash
php artisan serve
```
<section class="bg-gray-100 text-gray-800 py-10 px-6 rounded-lg shadow-md mt-10 max-w-3xl mx-auto">
  <h2 class="text-2xl font-semibold mb-4">📝 Notas del Proyecto</h2>
  <p class="mb-2">
    Este proyecto fue desarrollado con fines <strong>académicos y prácticos</strong>. Su estructura está pensada para facilitar el aprendizaje y adaptación a otros contextos.
  </p>
  <p>
    Puedes adaptarlo fácilmente a múltiples casos de uso como:
    <ul class="list-disc pl-5 mt-2">
      <li>Gestión de expedientes</li>
      <li>Archivos legales</li>
      <li>Sistemas internos de entidades públicas o privadas</li>
    </ul>
  </p>
</section>
