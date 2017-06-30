# rdm-tape-scripts

Sils rdm scripts to archive and restore projects to tape 

Basedirectory is /archive
 * Stuff to be archived should be put in /archive/upload
 * Archives are restored into /archive/restore
 * Meta data is stored in /archive/meta

The metadata is currently maintained in a database the dbcreate.sql can serve as a template to set it up

 List of tools and usage
1. LabelTape: Put info file at start of tape. Should be and can only be done before archiving stuff on the tape.
   At 4 or 5 arguments needed: 
    * POOL_ID      : Which group is the tape for. Groups database contains the list of valid groups.
    * TAPE_BARCODE : Barcode of the tape this should be equal to the barcode sticker on the tape including leading zeros
    * TAPE_CLASS   : Is it part of the primary archive or a copy for the group  (Primary/GroupCopy)
    * TAPE_NUMBER  : Tape number in the pool (Group) 
    * [FORCE]      : Use this parameter to force labeling a non-empty tape (overwriting all data on the tape) 

   example:  LabelTape Breit 000006 Primary 2 FORCE 

2. ReceiveRequest: Prepare archive. Prepare data form create directory for upload. Send a mail to requester telling where to upload data.
   * RequestID : Number assigned at the time of request.
   * user      : Username for which archive directory will be created.
   example: ReceiveRequest 10 jrauwer1
   

3. ArchiveToTape:  Archive project
   * RequestID         : Number assigned at the time of request.
   * TapeID            : Barcode of tape 
   * TapeLocationNumber: Location number on tape 
   * External ID       : External ID for the archive
   * [FORCE]           : Archive even if there is data at that location  (overwriting all data ont the tape from the given location onward)
 
   example: ArchiveToTape 10 000002 1 "Not assigned"
         
4. RestoreFromTape: Restore project
    * TapeID            : Barcode of tape 
    * TapeLocationNumber: Location number on tape 

   example: RestoreFromTape: 000002 1 
          
5. ListTape: List information about tape(s)
    * [TapeID] : Barcode of tape
   Without arguments gives overview of tapes 
 
   example: ListTape 000002
    
