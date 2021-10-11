# Changelog

---

- [v2.2.0](#v2.2.0)
- [v2.1.0](#v2.1.0)
- [v2.0.0](#v2.0.0)


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
