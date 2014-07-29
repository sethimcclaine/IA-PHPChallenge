Find the most common 3-page sequence from the corresponding Apache log.


Assumptions:
- Standard Apache access log.
- An IP is unique per user.
- Log entries are in order.
- 3 Page sequence: 3 consecutive page requests from a user.


Output:
- The sequence
- The number of times that sequence appeared in the Apache log.


Example: 
- Sequence (p1,p2,p3) was requested 2 times:

User A: Page 1

User B: Page 1
User B: Page 2
User B: Page 3
User B: Page 2

User A: Page 2
User A: Page 3
User A: Page 4
User A: Page 1
User A: Page 2
