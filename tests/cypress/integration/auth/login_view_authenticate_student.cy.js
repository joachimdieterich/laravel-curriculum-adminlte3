describe('Authenticate Student Test', () => {
    const username = 'student';
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
    })

});
