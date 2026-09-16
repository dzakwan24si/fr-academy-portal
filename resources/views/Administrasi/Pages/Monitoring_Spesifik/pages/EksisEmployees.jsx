import { useState } from "react";
import { EmployeeTable } from "../components/DataManagement/EmployeeTable";
import { EmployeeFormModal } from "../components/DataManagement/EmployeeFormModal";
import { useEmployees } from "../hooks/useEmployees";

export default function EksisEmployees() {
  const { data, loading, error, addEmployee, updateEmployee, deleteEmployee } = useEmployees(["Eksis", "Aktif"]);
  const [isFormOpen, setIsFormOpen] = useState(false);
  const [editingEmployee, setEditingEmployee] = useState(null);

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
      await deleteEmployee(id);
    } catch (err) {
      alert("Gagal menghapus data: " + err.message);
    }
  };

  const handleSubmitForm = async (formData) => {
    if (editingEmployee) {
      await updateEmployee(editingEmployee.ID_MONITORING, formData);
    } else {
      await addEmployee({ ...formData, STATUS: "Aktif" });
    }
  };

  if (error) {
    return <div className="p-4 bg-red-50 text-red-600 rounded-xl">Error loading data: {error}</div>;
  }

  return (
    <div className="h-full">
      <EmployeeTable 
        data={data}
        loading={loading}
        title="Data Karyawan Aktif"
        description="Daftar karyawan FR Academy yang masih aktif bekerja."
        onAdd={handleAdd}
        onEdit={handleEdit}
        onDelete={handleDelete}
      />

      <EmployeeFormModal 
        isOpen={isFormOpen}
        onClose={() => setIsFormOpen(false)}
        onSubmit={handleSubmitForm}
        initialData={editingEmployee}
      />
    </div>
  );
}
