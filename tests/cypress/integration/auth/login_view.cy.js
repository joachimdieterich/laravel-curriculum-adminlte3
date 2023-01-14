describe('Login View Test', () => {
    it('shows login view', () => {
        cy.visit('/login');
        cy.url().should('match', /login/);
        cy.contains('curriculum');
    });
});
