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

      //Go into subjects
      cy.visit('/subjects');

      //create new subjects
      cy.get('.nav-item-box-image-size > .fa')
      .click();
      cy.get('#title')
      .type('Testtitel');
      cy.get('#title_short')
      .type('TT');
      cy.get('#title_short')
      .click();
  })
})