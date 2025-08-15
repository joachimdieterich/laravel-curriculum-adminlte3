describe('Show Teachers Kanban Index Test', () => {
    const username = 'teacher';
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

        cy.request('/kanbans').then((response) => {
            expect(response.status).to.eq(200)
        });
        cy.visit('/kanbans');
    })

});
