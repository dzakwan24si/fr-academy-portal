import React from 'react';
import { createRoot } from 'react-dom/client';
import AsistenApp from './AsistenApp.jsx';

const AsistenPage = () => {
    return <AsistenApp />;
};

export default AsistenPage;

const container = document.getElementById('asisten-root');
if (container) {
    createRoot(container).render(
        <React.StrictMode>
            <AsistenPage />
        </React.StrictMode>
    );
}
