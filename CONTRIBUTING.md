Contributing to the Project
===========================

We welcome contributions and appreciate your help in maintaining and improving this project. Please follow the guidelines below to ensure consistency and quality.

Code Standards
--------------

- **Standards**: Follow the code standards laid out by the PHP_CodeSniffer file, which enforces PSR-12 with some additions.
- **Variables**: All variables should be declared in camelCase.
- **Interfaces**: Name interfaces without any postfixes or prefixes like `I` or `Interface`.

Branches and Merge Requests
----------------------------

- **Branches**: Make all changes in a separate branch from `main`.
- **Merge Requests**: Ensure your branch is up-to-date with `main` before submitting a merge request.
- **Review**: Wait for the code review process and address any feedback.

Testing
-------

- **Tests**: Write tests using Codeception.
- **Coverage**: Ensure 100% code coverage for all changes. Run `vendor/bin/codecept run --coverage --coverage-html` to generate a coverage report.

Thank you for your contributions!
