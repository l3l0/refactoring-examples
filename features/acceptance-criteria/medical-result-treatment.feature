Feature:
    In order to get financial support for the treatment for given medical result
    As a medical assistant
    I want to have possibility to decide if treatment is eligible for my medical fund

    Scenario: Decide about treatment for medical examination result
        Given I am medical assistant with id "36ff8d22-4bc1-4527-8596-f0e1a47e6b4b"
        And I am logged in using password "test123"
        And I already issued such medical examination order:
            | patientIdentificationNumber | 91081611797 |
            | agreementNumber             | UMOWA-01    |
        And such medical result for agreement number "UMOWA-01" was issued:
            | resultDocumentId     | f4cfd702-3449-45e7-a8cc-c4c059eed9b5 |
            | requiredDecisionDate | 2022-09-01T19:20:30Z                 |
        When I decide that treatment is needed for medical result with agreement number "UMOWA-01"
