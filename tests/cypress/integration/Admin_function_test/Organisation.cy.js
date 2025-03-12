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

      //Goes into Organisations and Creates a New one
      cy.visit('/organizations')
      cy.get('.nav-item-box-image-size > .fa')
      .click();
      cy.get('#title')
      .type('Test Titel');
      cy.get('#street')
      .type('Test Street');
      cy.get('#postcode')
      .type('123456');
      cy.get('#city')
      .type('Speyer');
      cy.get('#lms_url')
      .type('https:\\Testlink');
      cy.get('#phone')
      .type('1234567890');
      cy.get('#email')
      .type('Testemail@email.de');
      
      // Rest der Inputs verbugt (Notiert) muss nachgeschrieben werden wenn Bugs gefixt sind
      //Kann nicht Speichern, Speicher button kaputt, Test wird abgebrochen
  })
})