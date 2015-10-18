Email Service
===================

### Purpose
Currently the Email Service is being used for 2 instances:

1. Emailing invite codes (referrals) from registered users
2. Emailing codes for resetting passwords

There will most likely be other uses for the Email Service in the future (i.e. newsletters, promotions, etc.).

### Use Cases

Email Service connects to AWS API, which then connects to SES (Simple Email Service) to call on the sendEmail function. Email Service also uses Twig as a template to populate all the variables needed for the emails as well as a plain text template.

### Current State

Email Service successfully sends out both types of emails when tested locally but has not been tested running on a production server. Will update once tested.
