import { useState, useEffect } from "react";
import { CulledTable } from "../components/DataManagement/CulledTable";
import { CulledFormModal } from "../components/DataManagement/CulledFormModal";
import { AngkatanFormModal } from "../components/DataManagement/AngkatanFormModal";
import { ImportModal } from "../components/DataManagement/ImportModal";
import { useCulled } from "../hooks/useCulled";
import { useAngkatan } from "../hooks/useAngkatan";

export default function CulledEmployees() {
  const { 
    data, 
    angkatanList, 
    loading, 
    error, 
    addCulled, 
    updateCulled, 
    deleteCulled, 
    deleteAllCulled,
    importCulled,
    refetch: refetchCulled 
  } = useCulled();
  const { addAngkatan } = useAngkatan();
  
  const [isFormOpen, setIsFormOpen] = useState(false);
  const [isImportOpen, setIsImportOpen] = useState(false);
  const [editingEmployee, setEditingEmployee] = useState(null);
  
  const [isAngkatanModalOpen, setIsAngkatanModalOpen] = useState(false);

  useEffect(() => {
    const handleOpenAngkatanModal = () => setIsAngkatanModalOpen(true);
    window.addEventListener('openAngkatanModal', handleOpenAngkatanModal);
    return () => window.removeEventListener('openAngkatanModal', handleOpenAngkatanModal);
  }, []);

  const handleAdd = () => {
    setEditingEmployee(null);
    setIsFormOpen(true);
  };

  const handleEdit = (employee) => {
    setEditingEmployee(employee);
    setIsFormOpen(true);
  };

  const handleDelete = async (id) => {
    try {
      await deleteCulled(id);
    } catch (err) {
      alert("Gagal menghapus data: " + err.message);
    }
  };

  const handleDeleteAll = async () => {
    if (window.confirm("PERINGATAN: Apakah Anda yakin ingin menghapus SEMUA data peserta culled?\nSemua data peserta culled akan dikosongkan. Tindakan ini tidak dapat dibatalkan.")) {
      try {
        await deleteAllCulled();
        alert("Semua data peserta culled berhasil dihapus.");
      } catch (err) {
        alert("Gagal menghapus seluruh data: " + err.message);
      }
    }
  };

  const handleSubmitForm = async (formData) => {
    let finalAlasan = formData.alasan || "";
    
    // Clean up any existing tags first to prevent duplicate/conflicting tags when editing
    finalAlasan = finalAlasan.replace(/\[(In Class|OJT)\]\s*/ig, "").trim();
    
    // Inject fase_program into alasan if applicable
    if (formData.kategori_status === "Culled" && formData.fase_program) {
      finalAlasan = `[${formData.fase_program}] ${finalAlasan}`.trim();
    }

    const payload = {
      nama: formData.nama,
      id_angkatan: formData.id_angkatan,
      kategori_status: formData.kategori_status,
      Jenis_culled: formData.Jenis_culled || (formData.fase_program === "OJT" ? "Resign_OJT" : "Resign_In_Class"),
      alasan: finalAlasan
    };

    if (editingEmployee) {
      await updateCulled(editingEmployee.id_culled, payload);
    } else {
      await addCulled(payload);
    }
  };

  const handleAddAngkatan = async (formData) => {
    try {
      await addAngkatan(formData);
      // Refetch culled data so the angkatanList gets updated in the dropdown
      await refetchCulled();
    } catch (err) {
      throw err;
    }
  };

  if (error) {
    return <div className="p-4 bg-red-50 text-red-600 rounded-xl">Error loading data: {error}</div>;
  }

  const handleExport = () => {
    window.location.href = "/administrasi/monitoring/api/culled/export";
  };

  return (
    <div className="h-full">
      <CulledTable 
        data={(data || []).filter(item => item.nama !== 'Manual Entry')}
        loading={loading}
        title="Data Peserta Culled"
        description="Daftar peserta pelatihan FR Academy yang dikeluarkan atau tidak lulus."
        onAdd={handleAdd}
        onEdit={handleEdit}
        onDelete={handleDelete}
        onDeleteAll={handleDeleteAll}
        onOpenImport={() => setIsImportOpen(true)}
        onExport={handleExport}
      />

      <CulledFormModal 
        isOpen={isFormOpen}
        onClose={() => setIsFormOpen(false)}
        onSubmit={handleSubmitForm}
        initialData={editingEmployee}
        angkatanList={angkatanList}
      />

      {/* Quick Add Angkatan Modal */}
      <AngkatanFormModal 
        isOpen={isAngkatanModalOpen}
        onClose={() => setIsAngkatanModalOpen(false)}
        onSubmit={handleAddAngkatan}
      />

      {/* Import Modal */}
      <ImportModal
        isOpen={isImportOpen}
        onClose={() => setIsImportOpen(false)}
        onImport={importCulled}
        title="Import Data Staff Culled"
        description="Unggah file Excel / CSV data peserta culled. Kolom yang diharapkan: nama, angkatan, kategori_status, jenis_culled, alasan."
        templateUrl="/administrasi/monitoring/api/culled/template"
      />
    </div>
  );
}
