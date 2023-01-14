describe('kanban tests (teacher role)', () => {
    const username = 'teacher';
    const password = 'password';

    const title = 'UI-Test Kanban';
    const description = 'UI-Test Kanban - Description';
    let modelID = 0;
    context('CRUD', () => {
        //Authenticate Teacher
        beforeEach(() => {
            cy.visit('/login');
            cy.get('input[name="email"]')
                .type(username)
                .should('have.value', username)

                .get('input[name="password"]')
                .type(password)
                .should('have.value', password)

                .get('button[name="login"]')
                .click();
        });

        it('add new kanban', () => {
            cy.visit('/kanbans')
              .get('a[id="add-kanban"]')
              .click();

            cy.get('div[class="vue-swatches"]')
              .click()
              .get('div[aria-label="#3398DB"]')
              .click()

              .get('input[id="title"]')
              .type(title)
              .should('have.value', title);

            cy.window()
              .then(win => {
              win.tinymce.activeEditor.setContent("<p>" + description + "</p>");
            });
            cy.get('input[id="kanban-save"]')
              .click();
            cy.url().then((url) => {
              var parts = url.split('/');
              parent.modelId = parts.pop() || parts.pop();  // get modelId
            });
        });

        it('add new kanban status1', () => {
            cy.visit('/kanbans/'+ parent.modelId)
                .get('div[id="kanbanStatusCreate"]')
                .click()

                .get('button[name="kanbanStatusCancel"]')
                .click()

                .get('div[id="kanbanStatusCreate"]')
                .click()

                .get('input[id="title"]')
                .type('Status 1')
                .should('have.value', 'Status 1')

                .get('button[name="kanbanStatusSave"]')
                .click();
        });

        it('add new kanban status2', () => {
            cy.visit('/kanbans/'+ parent.modelId)
                .get('div[id="kanbanStatusCreate"]')
                .click()

                .get('button[name="kanbanStatusCancel"]')
                .click()

                .get('div[id="kanbanStatusCreate"]')
                .click()

                .get('input[id="title"]')
                .type('Status 2')
                .should('have.value', 'Status 2')

                .get('button[name="kanbanStatusSave"]')
                .click();
        });

        it('add new kanban item1', () => {
            cy.visit('/kanbans/'+ parent.modelId)
                .get('div[id="kanbanItemCreateButton_1"]')
                .click()

                .get('div[id="kanbanItemCreate_1"] div[class="vue-swatches"]')
                .click()
                .get('div[id="kanbanItemCreate_1"] div[aria-label="#3398DB"]')
                .click()

                .get('div[id="kanbanItemCreate_1"] input[id="title"]')
                .type('Item 1')
                .should('have.value', 'Item 1');

            cy.window()
                .then(win => {
                    win.tinymce.activeEditor.setContent("<p>Item 1 description</p>");
                });

            cy.get('div[id="kanbanItemCreate_1"] button[name="kanbanItemSave"]')
                .click();
        });

        it('add new kanban item1', () => {
            cy.visit('/kanbans/'+ parent.modelId)
                .get('div[id="kanbanItemCreateButton_1"]')
                .click()

                .get('div[id="kanbanItemCreate_1"] div[class="vue-swatches"]')
                .click()
                .get('div[id="kanbanItemCreate_1"] div[aria-label="#3398DB"]')
                .click()

                .get('div[id="kanbanItemCreate_1"] input[id="title"]')
                .type('Item 1')
                .should('have.value', 'Item 1');

            cy.window()
                .then(win => {
                    win.tinymce.activeEditor.setContent("<p>Item 1 description</p>");
                });

            cy.get('div[id="kanbanItemCreate_1"] button[name="kanbanItemSave"]')
                .click();
        });

        it('add new kanban item2', () => {
            cy.visit('/kanbans/'+ parent.modelId)
                .get('div[id="kanbanItemCreateButton_0"]')
                .click()

                .get('div[id="kanbanItemCreate_0"] div[class="vue-swatches"]')
                .click()
                .get('div[id="kanbanItemCreate_0"] div[aria-label="#3398DB"]')
                .click()

                .get('div[id="kanbanItemCreate_0"] input[id="title"]')
                .type('Item 2')
                .should('have.value', 'Item 2');

            cy.window()
                .then(win => {
                    win.tinymce.activeEditor.setContent("<p>Item 2 description</p>");
                });

            cy.get('div[id="kanbanItemCreate_0"] button[name="kanbanItemSave"]')
                .click();
        });

        it('edit new kanban item2', () => {
            cy.visit('/kanbans/'+ parent.modelId)
                .get('div[id="kanbanItemDropdown_0_0"]')
                .click()
                .get('button[name="kanbanItemEdit_0_0"]')
                .click()

                .get('input[id="title_0_0"]')
                .type('Item 2-edit');

            cy.window()
                .then(win => {
                    win.tinymce.activeEditor.setContent("<p>Item 2 description - edit</p>");
                });

            cy.get('button[name="kanbanItemSave_0_0"]')
                .click();
        });

        it('add medium to kanban item2', () => {
            cy.visit('/kanbans/'+ parent.modelId)
                .get('div[id="kanbanItemDropdown_0_0"]')
                .click()
                .get('button[name="kanbanItemAddMedia_0_0"]')
                .click()

                .get('div[id="medium-create-modal"] a[href="#upload"]')
                .click()

                .get('div[id="medium-create-modal"] input[type="file"]')
                .selectFile('tests/cypress/integration/media/example.png', {
                    action: 'drag-drop'
                })

                .get('button[name="medium-create-modal-submit"]')
                .click();
        });

        it('delete kanban item2', () => {
            cy.visit('/kanbans/'+ parent.modelId)
                .get('div[id="kanbanItemDropdown_0_0"]')
                .click()
                .get('button[name="kanbanItemDelete_0_0"]')
                .click()
        });

        it('delete new kanban status2', () => {
            cy.visit('/kanbans/'+ parent.modelId)
                .get('div[id="kanbanStatusDropdown_1"]')
                .click()

                .get('div[id="kanbanStatusDropdown_1"] button[name="kanbanStatusDelete"]')
                .click();
        });

        it('delete new kanban status1', () => {
            cy.visit('/kanbans/'+ parent.modelId)
                .get('div[id="kanbanStatusDropdown_0"]')
                .click()

                .get('div[id="kanbanStatusDropdown_0"] button[name="kanbanStatusDelete"]')
                .click();
        });

        it('check if kanban is visible on index view', () => {
            cy.visit('/kanbans')
              .contains(title)
              .contains(description);
        });

        it('delete kanban', () => {
            cy.visit('/kanbans')
              .get('button[id="delete-kanban-' + parent.modelId + '"]')
              .click();
        });
    });
});
