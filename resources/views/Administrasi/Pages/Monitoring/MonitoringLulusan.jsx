import React from 'react';
import { createRoot } from 'react-dom/client';
import MonitoringAlumniTab from './MonitoringAlumniTab.jsx';

const MonitoringLulusanPage = () => {
    return <MonitoringAlumniTab />;
};

export default MonitoringLulusanPage;

const container = document.getElementById('monitoring-lulusan-root');
if (container) {
    createRoot(container).render(
        <React.StrictMode>
            <MonitoringLulusanPage />
        </React.StrictMode>
    );
}
