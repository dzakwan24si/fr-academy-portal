import React from 'react';
import { createRoot } from 'react-dom/client';
import RekrutmenApp from '../Rekrutmen/RekrutmenApp.jsx';

const RekrutmenPage = () => {
    return <RekrutmenApp />;
};

export default RekrutmenPage;

const container = document.getElementById('rekrutmen-root');
if (container) {
    createRoot(container).render(
        <React.StrictMode>
            <RekrutmenPage />
        </React.StrictMode>
    );
}
