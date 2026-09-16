import { useState } from 'react';
import { createRoot } from 'react-dom/client';
import DataManagement from '../../../Top_Management/pages/DataManagement.jsx';
import { Upload } from 'lucide-react';

const ProgramBatch = () => {
  const [message, setMessage] = useState('');
  const [error, setError] = useState('');

  const handleImport = async (event) => {
    event.preventDefault();
    setMessage('');
    setError('');
    const form = new FormData(event.currentTarget);
    const response = await fetch('/administrasi/program-batch/import', {
      method: 'POST',
      headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '' },
      body: form,
    });
    if (response.redirected) {
      window.location.reload();
      return;
    }
    const result = await response.json().catch(() => null);
    if (!response.ok) setError(result?.message || 'Import gagal.');
  };

  return (
    <div className="space-y-6">
      <div className="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
        <div>
          <h1 className="text-xl font-bold text-slate-800">Program & Batch Training</h1>
          <p className="text-sm text-slate-500">Kelola data program training reguler dan non-reguler FR Academy</p>
        </div>
        <form onSubmit={handleImport} className="flex flex-wrap items-center gap-3" encType="multipart/form-data">
          <input
            name="file"
            type="file"
            accept=".csv,.xlsx"
            required
            className="text-xs text-slate-600 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100 cursor-pointer"
          />
          <button className="flex items-center gap-2 rounded-lg bg-green-600 px-4 py-2 text-sm font-semibold text-white hover:bg-green-700 transition-colors shadow-xs">
            <Upload size={16} /> Import Excel / CSV
          </button>
        </form>
      </div>

      {message && (
        <div className="p-4 bg-green-50 border border-green-200 rounded-xl text-sm font-medium text-green-700">
          {message}
        </div>
      )}
      {error && (
        <div className="p-4 bg-red-50 border border-red-200 rounded-xl text-sm font-medium text-red-600">
          {error}
        </div>
      )}

      <DataManagement />
    </div>
  );
};

export default ProgramBatch;

createRoot(document.getElementById('program-batch-root')).render(<ProgramBatch />);
