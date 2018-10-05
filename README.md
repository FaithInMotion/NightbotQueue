# Nightbot Queue
Simple queue system for Nightbot (requires your own server/database/domain to run)

## Setting it up
1. Make sure you have your own server, domain and database running and reachable.
2. Create a database to house your information. Example: `youtube_channels`
3. Add a table to your database for your channel. Example: `DoSoConfidently` or `dsc`
4. Upload these files to your server, directly on the domain root.
5. In `config.php`, change the values for your database connection to match your credentials.

## Create the Nightbot commands

### Command: !addme
```
!commands add !addme $(urlfetch http://www.example.com/add.php?channel=dsc&user=$(user))
```
Usage: `!nextup 3`

Definition: Pulls the top 3 names from the queue and deletes them from the database. Only allows one entry per user, until their entry is deleted by `!nextup`.

### Command: !showqueue
```
!commands add !showqueue $(urlfetch http://www.example.com/list.php?channel=dsc)
```
Usage: `!showqueue`

Definition: Lists up to 100 characters from the top of the queue. This is limited because the message size for the chat is limited.

### Command: !nextup INTEGER
```
!commands add !nextup $(urlfetch http://www.example.com/next.php?channel=dsc&count=$(query))
```
Usage: `!nextup 3`

Definition: Pulls the top 3 names from the queue and deletes them from the database

Note: Make sure this is set to Owner or Owner/Moderators only! You don't want random users coming in and deleting so many names out of the queue just because.

## Developer's notes for streamers
- Use the `!showqueue` command to check for names that may have persisted from the last stream.
- Use the `!nextup` command with a high number (like 1000) to clear the database at the beginning of your stream.

# TODO
- Clean up for sql injection (in case used through browser)
- Add "xss clean" style security to inputs
