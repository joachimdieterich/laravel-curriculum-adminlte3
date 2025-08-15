let fs = require('fs');

module.exports = {
    activateCypressEnvFile() {
        if (fs.existsSync('.env.cypress.cypress')) {
            fs.renameSync('.env.cypress', '.env.cypress.backup');
            fs.renameSync('.env.cypress.cypress', '.env.cypress');
        }

        return null;
    },

    activateLocalEnvFile() {
        if (fs.existsSync('.env.cypress.backup')) {
            fs.renameSync('.env.cypress', '.env.cypress.cypress');
            fs.renameSync('.env.cypress.backup', '.env.cypress');
        }

        return null;
    }
};
