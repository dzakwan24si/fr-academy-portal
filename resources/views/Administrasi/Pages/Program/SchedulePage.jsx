import { createRoot } from 'react-dom/client';
import Schedule from '../../../Top_Management/pages/Schedule.jsx';

const SchedulePage = () => {
  return (
    <div className="min-h-screen bg-slate-50 p-6">
      <Schedule />
    </div>
  );
};

export default SchedulePage;

createRoot(document.getElementById('schedule-root')).render(<SchedulePage />);
