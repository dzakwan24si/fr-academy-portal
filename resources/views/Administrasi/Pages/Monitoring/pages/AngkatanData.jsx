import { useState } from "react";
import { AngkatanTable } from "../components/DataManagement/AngkatanTable";
import { AngkatanFormModal } from "../components/DataManagement/AngkatanFormModal";
import { useAngkatan } from "../hooks/useAngkatan";

export default function AngkatanData() {
  const { data, loading, error, addAngkatan, updateAngkatan, deleteAngkatan } = useAngkatan();
  const [isFormOpen, setIsFormOpen] = useState(false);
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

  const handleSubmitForm = async (formData) => {
    if (editingAngkatan) {
      await updateAngkatan(editingAngkatan.id_angkatan, formData);
    } else {
      await addAngkatan(formData);
    }
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
      />

      <AngkatanFormModal 
        isOpen={isFormOpen}
        onClose={() => setIsFormOpen(false)}
        onSubmit={handleSubmitForm}
        initialData={editingAngkatan}
      />
    </div>
  );
}
