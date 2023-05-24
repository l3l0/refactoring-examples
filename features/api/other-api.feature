Feature: Medical result decision API

  Scenario: Successful medical result de
    Given Medical assistant "36ff8d22-4bc1-4527-8596-f0e1a47e6b4b" already issued such medical examination order:
      | patientIdentificationNumber | 91081611797 |
      | agreementNumber             | UMOWA-01    |
    And Medical assistant "36ff8d22-4bc1-4527-8596-f0e1a47e6b4b" already issued such medical result with "UMOWA-01" agreement number:
      | resultDocumentId     | f4cfd702-3449-45e7-a8cc-c4c059eed9b5 |
      | requiredDecisionDate | 2022-09-01T19:20:30Z                 |
      | token                | testUmowa01Token                     |
    When do "POST" request to "/api/medical-result/testUmowa01Token/decision" following data:
      | headers | CONTENT_TYPE=application/json, PHP_AUTH_USER=36ff8d22-4bc1-4527-8596-f0e1a47e6b4b, PHP_AUTH_PW=test123 |
    Then request status code is 200
