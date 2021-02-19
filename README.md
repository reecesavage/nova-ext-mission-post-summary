# nova-ext-mission-post-summary
A small text field above the Post Body that provides a summary of large posts. A TL;DR section.


## Installation

- Install Required Extensions.
- Copy the entire directory into `applications/extensions/nova_ext_mission_post_summary`.
- Add the following to `application/config/extensions.php`: - Be sure the `jquery` lines appear before `nova_ext_mission_post_summary`
```
$config['extensions']['enabled'][] = 'nova_ext_mission_post_summary';



### Manual Setup - If not using the method above.

- Run the following commands on your MySQL database:

```
ALTER TABLE nova_posts ADD COLUMN nova_ext_mission_post_summary TEXT NULL DEFAULT NULL;
ALTER TABLE nova_missions ADD COLUMN mission_ext_mission_post_summary_enable int(11) DEFAULT 0;
```