describe('template spec', () => {
  const username = 'admin';
  const password = 'password';

  it('with authorization', () => {
      cy.visit('/login');
      cy.get('input[name="email"]')
          .type(username)
          .should('have.value', username)

          .get('input[name="password"]')
          .type(password)
          .should('have.value', password)

          .get('button[name="login"]')
          .click();
      cy.url().should('match', /home/);
      
      //Creation of a Test Kanban
      cy.visit('/kanbans');
      cy.get('.nav-item-box-image-size > .fa')
        .click();
      cy.get('#title')
        .type('TestIntro')
        .should('have.value','TestIntro');
      cy.get('#description')
        .type('TestBox')
        .should('have.value','TestBox');
      cy.get(':nth-child(2) > .custom-control-label')
        .click();
      cy.get('#kanban-save')
      .click();
      // Goes into createt Kanban
      cy.get('#kanban-content .box:last-child')
      .click();

      //Rename the Kanban
      cy.get('a.btn-flat > .fa')
      .click();
      //Rename Button geht nicht

      //Create Entry
      cy.get('#kanbanStatusCreate_0 > .text-secondary')
      .click();
      cy.get('#title_0')
      .type('Test');
      cy.get('[filter=".ignore"] > .btn')
      .click();
      cy.get('#kanbanItemCreateButton_0 > .text-white')
      .click();
      cy.get('#title_39')
      .click();
      cy.get('.vue-swatches')
      .click();
      

      //Selfdeletion
      /*cy.visit('/kanbans');
      cy.get('.content > .row > :nth-child(2) .box:last-child .btn')
      .click()
      .get('.content > .row > :nth-child(2) .box:last-child .text-red')
      .click();
      cy.get('#confirm-save')
      .click();
      */
  })

  
});