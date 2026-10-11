/* =========================================================
   ENTITIES LISTING - SHAPE ONLY
   =========================================================
   Filter/search/sort UI dan tombol export sudah ada di
   index.php, tapi belum disambungkan ke data (belum ada
   get-entities.php dsb). File ini cuma memastikan tombolnya
   tidak error kalau diklik selagi backend belum dibuat.

   Setelah bentuk halaman & form.php di-review, langkah
   berikutnya: buat tabel database + API (get/create/update/
   delete/upload) + JS penuh, mengikuti pola
   admin/assets/js/individual.js.
========================================================= */

document.addEventListener("DOMContentLoaded", () => {
  const placeholderNotice = () => {
    alert(
      "Halaman ini belum tersambung ke database.\n\nLangkah selanjutnya: buatkan API + JS supaya data, filter, dan export-nya berfungsi.",
    );
  };

  document.getElementById("exportExcelBtn").addEventListener("click", placeholderNotice);
  document.getElementById("exportPdfBtn").addEventListener("click", placeholderNotice);
});
