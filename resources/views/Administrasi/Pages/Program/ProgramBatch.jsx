import { createRoot } from 'react-dom/client';
import DataManagement from '../../../Top_Management/pages/DataManagement.jsx';

const ProgramBatch = () => {
  return (
    <div className="space-y-6">
      <DataManagement />
    </div>
  );
};

export default ProgramBatch;

createRoot(document.getElementById('program-batch-root')).render(<ProgramBatch />);
