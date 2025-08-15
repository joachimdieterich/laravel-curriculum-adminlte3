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

      // Goes into Logs
      cy.visit('/logbooks')
      cy.get('.nav-item-box-image-size > .fa')
        .click();
      cy.get('#title')
        .type("Test");
        // ERINNERUNG: Muss noch test für den Editor bekommen
      cy.get('#logbook-save')
      .click();
      cy.get('.content > .row > :nth-child(2) .box:last-child')
      .click()

      //Creates Test Logbook and renames it
      cy.get('a.btn > .fa')
      .click();
      cy.get('#title')
      .type("Test Rename");
      // ERINNERUNG: Muss noch test Rename für den Editor bekommen
      cy.get('#logbook-save')
      .click();

      //Creates Logbook Entry and fills it with Test Data
      cy.get('.card-tools > .btn > .fa')
      .click();
      cy.get('#add-logbook-entry')
      .click();
      cy.get('#title')
      .type("TestTitel");
      cy.get('[data-test="dp-input"]')
      .click();
      cy.get('[data-test="Wed Oct 16 2024 00:00:00 GMT+0200 (Mitteleuropäische Sommerzeit)"] > .dp__cell_inner')
      .click();
      cy.get('[data-test="Thu Oct 24 2024 00:00:00 GMT+0200 (Mitteleuropäische Sommerzeit)"] > .dp__cell_inner')
      .click();
      cy.get('[data-test="select-button"]')
      .click();
      cy.get('#title_short')
      .click();



      //Selfdeletion
      cy.visit('/logbooks');
      cy.get('.content > .row > :nth-child(2) .box:last-child .btn')
      .click()
      .get('.content > .row > :nth-child(2) .box:last-child .text-red')
      .click();
      cy.get('#confirm-save')
      .click();

  })
})