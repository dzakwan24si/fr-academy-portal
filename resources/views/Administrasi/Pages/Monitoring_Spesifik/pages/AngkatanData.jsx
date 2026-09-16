import { useState } from "react";
import { AngkatanTable } from "../components/DataManagement/AngkatanTable";
import { AngkatanFormModal } from "../components/DataManagement/AngkatanFormModal";
import { ImportModal } from "../components/DataManagement/ImportModal";
import { useAngkatan } from "../hooks/useAngkatan";

export default function AngkatanData() {
  const { data, loading, error, addAngkatan, updateAngkatan, deleteAngkatan, deleteAllAngkatan, importAngkatan } = useAngkatan();
  const [isFormOpen, setIsFormOpen] = useState(false);
  const [isImportOpen, setIsImportOpen] = useState(false);
  const [editingAngkatan, setEditingAngkatan] = useState(null);

  const handleAdd = () => {
    setEditingAngkatan(null);
    setIsFormOpen(true);
  };

  const handleEdit = (angkatan) => {
    setEditingAngkatan(angkatan);
    setIsFormOpen(true);
  };

  const handleDelete = async (id) => {
    try {
      await deleteAngkatan(id);
    } catch (err) {
      alert("Gagal menghapus data: " + err.message);
    }
  };

  const handleDeleteAll = async () => {
    if (window.confirm("PERINGATAN: Apakah Anda yakin ingin menghapus SEMUA data angkatan?\nSemua data angkatan akan dikosongkan. Tindakan ini tidak dapat dibatalkan.")) {
      try {
        await deleteAllAngkatan();
        alert("Semua data angkatan berhasil dihapus.");
      } catch (err) {
        alert("Gagal menghapus seluruh data: " + err.message);
      }
    }
  };

  const handleSubmitForm = async (formData) => {
    if (editingAngkatan) {
      await updateAngkatan(editingAngkatan.id_angkatan, formData);
    } else {
      await addAngkatan(formData);
    }
  };

  const handleExport = () => {
    window.location.href = "/administrasi/monitoring/api/angkatan/export";
  };

  if (error) {
    return <div className="p-4 bg-red-50 text-red-600 rounded-xl">Error loading data: {error}</div>;
  }

  return (
    <div className="h-full">
      <AngkatanTable 
        data={data}
        loading={loading}
        title="Master Data Angkatan"
        description="Kelola data angkatan FR Academy. Lulus, Culled, dan sisa siswa dihitung otomatis dari database."
        onAdd={handleAdd}
        onEdit={handleEdit}
        onDelete={handleDelete}
        onDeleteAll={handleDeleteAll}
        onOpenImport={() => setIsImportOpen(true)}
        onExport={handleExport}
      />

      <AngkatanFormModal 
        isOpen={isFormOpen}
        onClose={() => setIsFormOpen(false)}
        onSubmit={handleSubmitForm}
        initialData={editingAngkatan}
      />

      <ImportModal
        isOpen={isImportOpen}
        onClose={() => setIsImportOpen(false)}
        onImport={importAngkatan}
        title="Import Data Master Angkatan"
        description="Unggah file Excel / CSV data angkatan. Kolom yang diharapkan: angkatan, Jumlah Awal, lulus, Resign_In_Class, Resign_OJT, tidak_lulus."
        templateUrl="/administrasi/monitoring/api/angkatan/template"
      />
    </div>
  );
}
