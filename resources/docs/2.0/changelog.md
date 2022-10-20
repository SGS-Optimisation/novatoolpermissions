
# Changelog

---
- [v2.4.0](#v2.4.0)
- [v2.3.0](#v2.3.0)
- [v2.2.6](#v2.2.6)
- [v2.2.5](#v2.2.5)
- [v2.2.4](#v2.2.4)
- [v2.2.3](#v2.2.3)
- [v2.2.2](#v2.2.2)
- [v2.2.1](#v2.2.1)
- [v2.2.0](#v2.2.0)
- [v2.1.0](#v2.1.0)
- [v2.0.0](#v2.0.0)

<a name="v2.4.0"></a>
## v2.4.0 (2022-11-01)
- Added PM Rules
- PM Rules search allows search by job number, client account, or selected taxonomy terms
- From client account taxonomies, select which taxonomy to use for autocomplete in PM Search 
- Prod rules: Detect end user and job team conflict, and match job team to that of user, else to the job team marked "in use" in MySGS.   
  Fallback to manual account selection.

<a name="v2.3.0"></a>
## v2.3.0 (2022-08-12)
- Framework upgrades
- Client side caching and background update on socket notification
- Better server side caching
- Etag on API routes
- Adds feature to allow users to choose which account to use when no end user match
- Fix styling for headers in rule content (h1, h2, h3)

<a name="v2.2.6"></a>
## v2.2.6 (2022-07-07)
- OP View filters: add counter on number of new or updated items
- OP View: hide new or updated buttons when no content
- Better jobteam association by fetching user's jobteams and crossing with jobteams for a job


- <a name="v2.2.5"></a>
## v2.2.5 (2022-03-03)
- Adds optional client account matching using jobteam
- Improved client account creation with taxonomy selection and team owner
- Adds suggestion of existing taxonomies when adding in client account
- Improved and simplified team invite
- Fix for when jobs have no stage
  
<a name="v2.2.4"></a>
## v2.2.4 (2022-01-25)
- Added Matomo API integration
- Added stats job activity stats and visits stats 
- Client Account creation form now allows selecting from existing taxonomies
- Adds job/client/stage report with emailing to configurable list

<a name="v2.2.3"></a>
## v2.2.3 (2021-11-11)
- Add print process endpoint and mapper
- Fix for auto clearing jobs when customer not found
  
<a name="v2.2.2"></a>
## v2.2.2 (2021-11-10)
- Added mass create terms from PM section, with cleanup and auto-aliasing feature
- Added MySGS endpoints for tech spec
- Added rule import from Infinity kanban boards

<a name="v2.2.1"></a>
## v2.2.1 (2021-10-19)
- Fixes Stage filter not appearing after job's first load
- Handle multiple latest stages from MySGS
- Replace reset stage button by clear stage
- Fix issue handling team invite links from email
- Fix unselecting multiselect category not saved in PM section
- Fix OP job rules display in Firefox
- Fix team owners not having mass publish rule opion


<a name="v2.2.0"></a>
## v2.2.0 (2021-10-11)
- Frontend framework upgrade (Vue3)
- Team invitations: users receive mail to join a team; no longer need to wait for user to sign in once
- Updated file upload component
- PM Rules:
    - Select all/none
    - Mass unpublish, with option to choose target status: draft or reviewing
    - sorting by rule name/updated/created, ascending or descending
    - status search moved next to sorting field
- Optionally hide flagged rule content (system-wide), or at least show a big warning banner above content
- Team auto-switching, if belonging to team
- Fix permission issues for team owners
- "Rules in review" daily reminder emails to publishers in teams
- Prevent rules from transitioning to published if tagging error
- OP job search:
    - No animation in rules filtering (removed Isotope)
    - Checkbox next to rule to follow own progress on a given job, saved in browser session (refresh-proof)
- Matomo tracking feature, to track job views


<a name="v2.1.0"></a>
## v2.1.0 (2021-08-23)
- Multi-team feature
- Global stats
- Team level stats
- File uploads
- Job Stage tagging and filtering
- Flagged job mail notifications and weekly mail reminder
- Audit logs for rule tags (in addition of rule content)

<a name="v2.0.0"></a>
## v2.0.0 (2012-06-03)
Initial release.
