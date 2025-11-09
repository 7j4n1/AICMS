// Import Dependencies
import { useEffect, useState, useCallback } from "react";
import { Link } from "react-router";
import { PlusIcon, MagnifyingGlassIcon, PencilIcon, TrashIcon, EyeIcon } from "@heroicons/react/24/outline";
import { toast } from "sonner";

// Local Imports
import { Page } from "components/shared/Page";
import { Button, Card, Input } from "components/ui";
import { membersAPI } from "services/api";

// ----------------------------------------------------------------------

export default function MembersList() {
 const [members, setMembers] = useState([]);
 const [loading, setLoading] = useState(true);
 const [search, setSearch] = useState("");
 const [currentPage, setCurrentPage] = useState(1);
 const [pagination, setPagination] = useState({});


 const fetchMembers = useCallback(async () => {
   try {
     setLoading(true);
     const response = await membersAPI.getAll({
       search,
       page: currentPage,
       per_page: 15,
     });
     
     // Handle Laravel API response format
     const data = response.data?.data?.data || response.data?.data || [];
     const meta = response.data?.data?.pagination || response.data?.meta || {};
     
     setMembers(data);
     setPagination(meta);
   } catch (error) {
     console.error("Error fetching members:", error);
     toast.error("Failed to load members");
   } finally {
     setLoading(false);
   }
 }, [search, currentPage]);

  useEffect(() => {
    fetchMembers();
  }, [fetchMembers]);


 const handleDelete = async (id) => {
   if (!window.confirm("Are you sure you want to delete this member?")) {
     return;
   }

   try {
     await membersAPI.delete(id);
     toast.success("Member deleted successfully");
     fetchMembers();
   } catch (error) {
     console.error("Error deleting member:", error);
     toast.error("Failed to delete member");
   }
 };

 return (
   <Page title="Members">
     <div className="space-y-5 mx-4 my-4">
       {/* Header */}
       <div className="flex items-center justify-between">
         <div>
           <h2 className="text-2xl font-bold text-gray-800 dark:text-dark-100">
             Members Management
           </h2>
           <p className="mt-1 text-sm text-gray-500 dark:text-dark-300">
             Manage cooperative members
           </p>
         </div>
         <Link to="/members/create">
           <Button color="primary" className="gap-2">
             <PlusIcon className="size-5" />
             Add Member
           </Button>
         </Link>
       </div>

       {/* Search */}
       <Card className="p-5">
         <Input
           placeholder="Search by surname, name, or Coop ID..."
           value={search}
           onChange={(e) => setSearch(e.target.value)}
           prefix={
             <MagnifyingGlassIcon className="size-5 text-gray-400" />
           }
         />
       </Card>

       {/* Members Table */}
       <Card className="overflow-hidden">
         <div className="overflow-x-auto">
           <table className="w-full">
             <thead className="bg-gray-50 dark:bg-dark-600">
               <tr>
                 <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-dark-300">
                   Coop ID
                 </th>
                 <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-dark-300">
                   Name
                 </th>
                 <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-dark-300">
                   Phone
                 </th>
                 <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-dark-300">
                   Occupation
                 </th>
                 <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-dark-300">
                   Year Joined
                 </th>
                 <th className="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-dark-300">
                   Actions
                 </th>
               </tr>
             </thead>
             <tbody className="divide-y divide-gray-200 bg-white dark:divide-dark-500 dark:bg-dark-700">
               {loading ? (
                 <tr>
                   <td colSpan="6" className="px-6 py-4 text-center">
                     Loading...
                   </td>
                 </tr>
               ) : members.length === 0 ? (
                 <tr>
                   <td colSpan="6" className="px-6 py-4 text-center text-gray-500">
                     No members found
                   </td>
                 </tr>
               ) : (
                 members.map((member) => (
                   <tr key={member.id} className="hover:bg-gray-50 dark:hover:bg-dark-600">
                     <td className="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900 dark:text-dark-100">
                       {member.coopId}
                     </td>
                     <td className="whitespace-nowrap px-6 py-4 text-sm text-gray-500 dark:text-dark-300">
                       {member.surname} {member.otherNames}
                     </td>
                     <td className="whitespace-nowrap px-6 py-4 text-sm text-gray-500 dark:text-dark-300">
                       {member.phoneNumber}
                     </td>
                     <td className="whitespace-nowrap px-6 py-4 text-sm text-gray-500 dark:text-dark-300">
                       {member.occupation}
                     </td>
                     <td className="whitespace-nowrap px-6 py-4 text-sm text-gray-500 dark:text-dark-300">
                       {member.yearJoined}
                     </td>
                     <td className="whitespace-nowrap px-6 py-4 text-right text-sm font-medium">
                       <div className="flex items-center justify-end gap-2">
                         <Link to={`/members/${member.id}`}>
                           <Button size="sm" variant="outlined" className="gap-1">
                             <EyeIcon className="size-4" />
                             View
                           </Button>
                         </Link>
                         <Link to={`/members/${member.id}/edit`}>
                           <Button size="sm" variant="outlined" className="gap-1">
                             <PencilIcon className="size-4" />
                             Edit
                           </Button>
                         </Link>
                         <Button
                           size="sm"
                           variant="outlined"
                           color="error"
                           className="gap-1"
                           onClick={() => handleDelete(member.id)}
                         >
                           <TrashIcon className="size-4" />
                           Delete
                         </Button>
                       </div>
                     </td>
                   </tr>
                 ))
               )}
             </tbody>
           </table>
         </div>

         {/* Pagination */}
         {pagination.total > 0 && (
           <div className="flex items-center justify-between border-t border-gray-200 bg-white px-6 py-3 dark:border-dark-500 dark:bg-dark-700">
             <div className="text-sm text-gray-500 dark:text-dark-300">
               Showing {((currentPage - 1) * pagination.per_page) + 1} to{" "}
               {Math.min(currentPage * pagination.per_page, pagination.total)} of{" "}
               {pagination.total} results
             </div>
             <div className="flex gap-2">
               <Button
                 size="sm"
                 variant="outlined"
                 disabled={currentPage === 1}
                 onClick={() => setCurrentPage(currentPage - 1)}
               >
                 Previous
               </Button>
               <Button
                 size="sm"
                 variant="outlined"
                 disabled={currentPage >= pagination.total_pages}
                 onClick={() => setCurrentPage(currentPage + 1)}
               >
                 Next
               </Button>
             </div>
           </div>
         )}
       </Card>
     </div>
   </Page>
 );
}