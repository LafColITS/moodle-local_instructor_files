@local @local_instructor_files
Feature: Local instructor files allows a teacher to download teaching materials from a course
  In order to download files
  As a teacher
  There must be files in the course

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Terry     | Teacher  | teacher1@example.com |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |

  Scenario: Ensure graceful failure when no files are present
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to "Download instructor files" node in "Course administration"
    Then I should see "There are no files to download"
