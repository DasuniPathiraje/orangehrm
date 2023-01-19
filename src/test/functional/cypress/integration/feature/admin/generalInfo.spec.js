/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA  02110-1301, USA
 */

describe('Admin - Organization General Information', function () {
  beforeEach(function () {
    cy.task('db:reset');
    cy.fixture('chars').as('strings');
    cy.intercept('GET', '**/api/v2/admin/organization*').as('getOrganization');
    cy.intercept('POST', '**/api/v2/admin/organization').as('postOrganization');
    cy.fixture('user').then(({admin}) => {
      this.user = admin;
    });
  });

  // Read
  describe('Verify Navigation', function () {
    it('general info fields are loaded', function () {
      cy.loginTo(this.user, '/admin/viewOrganizationGeneralInformation');
      cy.wait('@getOrganization');
      cy.getOXD('pageTitle').should('include.text', 'General Information');
    });
  });

  //Add info
  describe('Verify add/edit detail(s) and toast message', function () {
    it('Verify multiple updates', function () {
      cy.loginTo(this.user, '/admin/viewOrganizationGeneralInformation');
      cy.wait('@getOrganization');
      // eslint-disable-next-line cypress/no-force
      cy.get('.oxd-switch-input').click({force: true});
      cy.getOXD('form').within(() => {
        cy.getOXDInput('Organization Name').type(this.strings.chars30.text);
        cy.getOXDInput('Registration Number').type(this.strings.chars10.int);
        cy.getOXDInput('Tax ID').type(this.strings.chars10.int);
        cy.getOXDInput('Phone').type(this.strings.chars10.int);
        cy.getOXDInput('Fax').type(this.strings.chars10.int);
        cy.getOXDInput('Email').type('admin@example.com');
        cy.getOXDInput('Address Street 1').type(this.strings.chars30.text);
        cy.getOXDInput('Address Street 2').type(this.strings.chars50.text);
        cy.getOXDInput('City').type(this.strings.chars10.text);
        cy.getOXDInput('State/Province').type(this.strings.chars10.text);
        cy.getOXDInput('Zip/Postal Code').type(this.strings.chars10.text);
        cy.getOXDInput('Country').selectOption('Canada');
        cy.getOXDInput('Notes').type(this.strings.chars120.text);
        cy.getOXD('button').contains('Save').click();
      });
      cy.toast('success', 'Successfully Updated');
    });

    it('Validate require fields', function () {
      cy.loginTo(this.user, '/admin/viewOrganizationGeneralInformation');
      cy.wait('@getOrganization');
      // eslint-disable-next-line cypress/no-force
      cy.getOXD('switch').click({force: true});
      cy.getOXD('form').within(() => {
        cy.getOXDInput('Organization Name').setValue('').isInvalid('Required');
      });
    });

    it('Validate character limit of fields', function () {
      cy.loginTo(this.user, '/admin/viewOrganizationGeneralInformation');
      cy.wait('@getOrganization');
      // eslint-disable-next-line cypress/no-force
      cy.getOXD('switch').click({force: true});
      cy.getOXD('form').within(() => {
        cy.getOXDInput('Organization Name')
          .setValue(this.strings.chars120.text)
          .isInvalid('Should not exceed 100 characters');
        cy.getOXDInput('Registration Number')
          .setValue(this.strings.chars50.text)
          .isInvalid('Should not exceed 30 characters');
        cy.getOXDInput('Tax ID')
          .setValue(this.strings.chars50.text)
          .isInvalid('Should not exceed 30 characters');
        cy.getOXDInput('Phone')
          .setValue(this.strings.chars50.text)
          .isInvalid('Should not exceed 30 characters');
        cy.getOXDInput('Fax')
          .setValue(this.strings.chars50.text)
          .isInvalid('Should not exceed 30 characters');
        cy.getOXDInput('Email')
          .setValue(this.strings.chars50.text)
          .isInvalid('Should not exceed 30 characters');
        cy.getOXDInput('Address Street 1')
          .setValue(this.strings.chars120.text)
          .isInvalid('Should not exceed 100 characters');
        cy.getOXDInput('Address Street 2')
          .setValue(this.strings.chars120.text)
          .isInvalid('Should not exceed 100 characters');
        cy.getOXDInput('City')
          .setValue(this.strings.chars50.text)
          .isInvalid('Should not exceed 30 characters');
        cy.getOXDInput('State/Province')
          .setValue(this.strings.chars50.text)
          .isInvalid('Should not exceed 30 characters');
        cy.getOXDInput('Zip/Postal Code')
          .setValue(this.strings.chars50.text)
          .isInvalid('Should not exceed 30 characters');
        cy.getOXDInput('Notes')
          .setValue(this.strings.chars400.text)
          .isInvalid('Should not exceed 255 characters');
      });
    });

    it('Validatee the characters accepted by the system for fields', function () {
      cy.loginTo(this.user, '/admin/viewOrganizationGeneralInformation');
      cy.wait('@getOrganization');
      // eslint-disable-next-line cypress/no-force
      cy.getOXD('switch').click({force: true});
      cy.getOXD('form').within(() => {
        cy.getOXDInput('Phone')
          .setValue(this.strings.chars10.text)
          .isInvalid('Allows numbers and only + - / ( )');
        cy.getOXDInput('Fax')
          .setValue(this.strings.chars10.text)
          .isInvalid('Allows numbers and only + - / ( )');
        cy.getOXDInput('Email')
          .setValue(this.strings.chars10.text)
          .isInvalid('Expected format: admin@example.com');
      });
    });
  });
});
